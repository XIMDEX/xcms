<?php

/*******************************************************************************
 * PARAMETERS
 *******************************************************************************/
const STRICT_MODE = true; // Fail if any metadata found in files is not in METADATA const
const ID_GROUP = 1;
const TEST_MODE = false;

// Mapper metadata to id_metadata

$METADATA = [
    'Título' => 11,
    'Fecha' => [
        'id' => 1,
        'parser' => function ($val) {
            return date('Y-m-d', strtotime($val));
        }
    ],
    'Autor' => 2,
    'Imagen' => 10
];

/*******************************************************************************
 * END PARAMETERS
 *******************************************************************************/
class MetadataFileToDatabase
{

    public $params;
    public $db;
    public $dir;
    public $relMetadataGroupMetadata;

    public function __construct()
    {
        $params = require '../conf/install-params.conf.php';
        $this->params = $params['db'];
        $this->db = $this->connect();
        $this->dir = '../data/files/';
        $this->relMetadataGroupMetadata = [];
    }

    private function connect(): mysqli
    {
        $conn = new mysqli($this->params['host'], $this->params['user'], $this->params['password'], $this->params['db']);
        $conn->set_charset("utf8");
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    public function execute()
    {
        $ids = 'SELECT max(IdVersion) FROM Versions GROUP BY IdNode';
        $sql = "SELECT Versions.IdNode, File FROM Versions WHERE File IS NOT NULL AND Versions.IdVersion IN ($ids)";

        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $file = "{$this->dir}{$row['File']}.metadata";
                if (file_exists($file)) {
                    $this->getMetadata($row['IdNode'], $file);
                }
            }
        }
    }

    private function getMetadata(string $nodeId, string $file)
    {
        global $METADATA;

        $data = file_get_contents($file);
        if ($data === FALSE) {
            throw new Exception("Fail read {$file}");
        }
        $data = json_decode($data, true);
        if (count($data) > 0) {
            foreach ($data as $field => $value) {
                if (!in_array($field, array_keys($METADATA))) {
                    if (STRICT_MODE)
                        exit("NOT FOUND {$field}");
                } elseif (!empty($value)) {
                    $value = isset($METADATA[$field]['parser']) ? $METADATA[$field]['parser']($value) : $value;
                    $field = $METADATA[$field]['id'] ?? $METADATA[$field];
                    $this->insertOrUpdate($field, $value, $nodeId);
                }
            }
        }
    }

    private function existMetadata($nodeId, $idRel)
    {
        $sql = "SELECT * FROM MetadataValue WHERE IdNode = {$nodeId} and IdRelMetadataGroupMetadata = {$idRel}";
        $result = $this->db->query($sql);
        return $result->num_rows > 0;
    }

    private function getRelMetadataGroupMetadata($id_metadata): string
    {
        if (!in_array($id_metadata, array_keys($this->relMetadataGroupMetadata))) {

            $sql = "SELECT idRelMetadataGroupMetadata FROM RelMetadataGroupMetadata WHERE idMetadata = {$id_metadata} and idMetadataGroup = " . ID_GROUP;

            $result = $this->db->query($sql);
            if ($result->num_rows == 0)
                throw new Exception("RelMetadataGroupMetadata not found idMetadata = {$id_metadata} and idMetadataGroup = " . ID_GROUP);

            $this->relMetadataGroupMetadata[$id_metadata] = mysqli_fetch_assoc($result)['idRelMetadataGroupMetadata'];
        }
        return $this->relMetadataGroupMetadata[$id_metadata];
    }

    private function insertOrUpdate($field, $value, $nodeId)
    {
        $idRel = $this->getRelMetadataGroupMetadata($field);
        if ($this->existMetadata($nodeId, $idRel)) {
            $sql = "UPDATE MetadataValue SET value='%s' WHERE IdNode = '{$nodeId}' AND IdRelMetadataGroupMetadata = '{$idRel}'";
        } else {
            $sql = "INSERT INTO MetadataValue (IdNode, IdRelMetadataGroupMetadata, value) VALUES ('{$nodeId}', '{$idRel}', '%s')";
        }
        $sql = sprintf($sql, mysqli_real_escape_string($this->db, $value));
        if (TEST_MODE) {
            echo $sql . "\n";
        } else {
            $this->db->query($sql);
        }
    }
}

$m = new MetadataFileToDatabase();
$m->execute();
