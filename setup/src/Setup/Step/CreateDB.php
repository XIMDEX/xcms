<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 28/05/16
 * Time: 11:19
 */

namespace Ximdex\Setup\Step;


use PDO;
use PDOException;
use Ximdex\Setup\Manager;

class CreateDB extends Base
{
    private $db = null;

    public function __construct(Manager $manager)
    {
        parent::__construct($manager);
        $this->label = "Create tables";
        $this->template = "createtables.twig";
        $this->title = "Create Ximdex CMS Tables and data";
        $this->vars['title'] = $this->title;

    }

    public function checkErrors()
    {


        parent::checkErrors();

        $this->checkDBConnection();
        $this->importTables();


    }

    /**
     * Methods to check
     */
    private function checkDBConnection()
    {
        $form = $_SESSION['db'];
        $valid = true;

        try {
            $pdconnstring = "mysql:host={$form['dbhost']};port={$form['dbport']};dbname={$form['dbname']}" ;
            $this->db = new PDO($pdconnstring, $form['dbuser'], $form['dbpass']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        } catch (PDOException $e) {
            $valid = false;
        }


        if ($valid === false) {
            $this->addError(
                sprintf("Unable to connect to database"),
                sprintf("Unable to connect to database. Please check settings and try again"),
                "DB"
            );

        }
    }

    private function importTables()
    {

        if (empty($this->db)) {
            return;
        }
        $data = file_get_contents($this->manager->getFullPath("/data/sql/Ximdex_3.6_schema.sql"));
        $data .= file_get_contents($this->manager->getFullPath("/data/sql/Ximdex_3.6_data.sql"));

        try {
            $statement = $this->db->prepare($data);
            $statement->execute();
            while ($statement->nextRowset()) {/* https://bugs.php.net/bug.php?id=61613 */
            };
            $this->db->exec("UPDATE Config SET ConfigValue = '{$this->manager->getInstallRoot()}' WHERE ConfigKey = 'AppRoot'");
            $urlRoot = str_replace("index.php", "", $_SERVER['HTTP_REFERER']);
            $urlRoot = str_replace("setup/", "", $urlRoot);
            $urlRoot = strtok($urlRoot, '?');
            $this->db->exec("UPDATE Config SET ConfigValue = '{$urlRoot}' WHERE ConfigKey = 'UrlRoot'");
            $this->db->exec("UPDATE Config SET ConfigValue = 'en_US' WHERE ConfigKey = 'locale'");
            // create conf file
            $modConfStr = $this->manager->render('files/install-params.conf.php.twig', [
                    'db' => $_SESSION['db'],
                    'rootPath' => $this->manager->getRootPath("/"),
                ]
            );
            file_put_contents($this->manager->getRootPath('/conf/install-params.conf.php'), $modConfStr);

            // generate xid

            $hostName = $_SERVER["HTTP_HOST"];
            $url = "http://xid.ximdex.net/stats/getximid.php?host=$hostName";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
            ));
            $resp = curl_exec($curl);
            curl_close($curl);
            if ($resp === false || empty($resp)) {
                $this->db->execute("UPDATE Config SET ConfigValue='{$resp}' where ConfigKey='ximid'");
            }

        } catch (PDOException $e) {
            $this->addError(
                sprintf("Unable to create tables "),
                sprintf("Unable to create tables and data, database must be empty, Check database permissions"),
                "DB"
            );

        }


    }


}