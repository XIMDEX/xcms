<?php

interface Exporter
{
    public function DeleteAll();

    public function ExportAll();

    public function ExportByNodeId($nodeid);

    public function CreateSchema();

    public function DeleteSchema();
}