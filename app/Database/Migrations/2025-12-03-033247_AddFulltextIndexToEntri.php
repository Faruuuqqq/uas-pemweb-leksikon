<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFulltextIndexToEntri extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE entri ADD FULLTEXT entri_fulltext(term, definition)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE entri DROP INDEX entri_fulltext');
    }
}
