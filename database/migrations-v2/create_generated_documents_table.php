<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('generated_documents', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->index();

            $table->morphs('documentable');

            $table->string('document_class');
            $table->text('content')->nullable();

            // saved storage file
            $table->string('disk')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();

            $table->json('resolved_placeholder')->nullable();
            $table->json('extra')->nullable();

            // timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('generated_documents');
    }
};
