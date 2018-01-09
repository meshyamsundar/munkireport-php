<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

class Network extends Migration
{
    private $tableName = 'network';
    private $tableNameV2 = 'network_v2';

    public function up()
    {
        $capsule = new Capsule();
        $migrateData = false;

        if ($capsule::schema()->hasTable($this->tableNameV2)) {
            // Migration already failed before, but didnt finish
            throw new Exception("previous failed migration exists");
        }

        if ($capsule::schema()->hasTable($this->tableName)) {
            $capsule::schema()->rename($this->tableName, $this->tableNameV2);
            $migrateData = true;
        }

        $capsule::schema()->create($this->tableName, function (Blueprint $table) {
            $table->increments('id');

            $table->string('serial_number')->nullable();
            $table->string('service')->nullable();
            $table->integer('order')->nullable();
            $table->integer('status')->nullable();
            $table->string('ethernet')->nullable();
            $table->string('clientid')->nullable();
            $table->string('ipv4conf')->nullable();
            $table->string('ipv4ip')->nullable();
            $table->string('ipv4mask')->nullable();
            $table->string('ipv4router')->nullable();
            $table->string('ipv6conf')->nullable();
            $table->string('ipv6ip')->nullable();
            $table->integer('ipv6prefixlen')->nullable();
            $table->string('ipv6router')->nullable();

            $table->index('serial_number');
            $table->index(['serial_number', 'service']);
            $table->index('service');
            $table->index('ethernet');
            $table->index('ipv4ip');
            $table->index('ipv4router');
            $table->index('ipv4mask');
        });

        if ($migrateData) {
            $capsule::select("INSERT INTO 
                $this->tableName
            SELECT
                id,
                serial_number,
                service,
                `order`,
                status,
                ethernet,
                clientid,
                ipv4conf,
                ipv4ip,
                ipv4mask,
                ipv4router,
                ipv6conf,
                ipv6ip,
                ipv6prefixlen,
                ipv6router
            FROM
                $this->tableNameV2");
        }
    }

    public function down()
    {
        $capsule = new Capsule();
        $capsule::schema()->dropIfExists($this->tableName);
        if ($capsule::schema()->hasTable($this->tableNameV2)) {
            $capsule::schema()->rename($this->tableNameV2, $this->tableName);
        }
    }
}
