<?php

/**
 * @file classes/migration/CopyPagesSchemaMigration.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class CopyPagesSchemaMigration
 * @brief Describe database table structures.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

class CopyPagesSchemaMigration extends Migration {
        /**
         * Run the migrations.
         * @return void
         */
        public function up() {
		// List of copy pages for each context
		Capsule::schema()->create('copy_pages', function (Blueprint $table) {
			$table->bigInteger('copy_page_id')->autoIncrement();
			$table->string('path', 255);
			$table->bigInteger('context_id');
		});

		// Copy Page settings.
		Capsule::schema()->create('copy_page_settings', function (Blueprint $table) {
			$table->bigInteger('copy_page_id');
			$table->string('locale', 14)->default('');
			$table->string('setting_name', 255);
			$table->longText('setting_value')->nullable();
			$table->string('setting_type', 6)->comment('(bool|int|float|string|object)');
			$table->index(['copy_page_id'], 'copy_page_settings_copy_page_id');
			$table->unique(['copy_page_id', 'locale', 'setting_name'], 'copy_page_settings_pkey');
		});

	}
}