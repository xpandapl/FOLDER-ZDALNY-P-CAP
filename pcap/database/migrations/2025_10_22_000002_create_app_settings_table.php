<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAppSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, email, url
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default welcome text
        DB::table('app_settings')->insert([
            [
                'key' => 'welcome_text',
                'value' => '<p><strong>If you are an English speaker and you need an English version of this form, please use your browser translate tool.</strong></p>
<ul style="text-align:left; margin: 12px auto; max-width: 760px;">
    <li>Poniżej znajdziesz formularz do samooceny - każdy pracownik wykonuje to samodzielnie.</li>
    <li>Zarezerwuj czas: między 20 min a 1 h - w zależności od poziomu rozwoju zawodowego.</li>
    <li>Oceń siebie z perspektywy kompetencji osobistych, społecznych, zawodowych, liderskich do poziomu, na którym jesteś, tj. Junior, Specjalista, Senior, Supervisor, Manager.</li>
    <li>Przy każdej kompetencji możesz zostawić przykład wykorzystania danej kompetencji w pracy (krótko i na faktach).</li>
</ul>
<p>Oceń siebie za ten rok pracy (lub krótszy okres, jaki z nami jesteś). Twoja ocena trafi do Twojego lidera/liderki do dalszego etapu procesu P-CAP.</p>',
                'type' => 'textarea',
                'label' => 'Tekst powitalny na stronie startowej',
                'description' => 'Instrukcje dla użytkowników na początku procesu samooceny',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_name',
                'value' => 'Asi Tonkowicz',
                'type' => 'text',
                'label' => 'Imię i nazwisko osoby kontaktowej',
                'description' => 'Osoba do kontaktu w sprawie problemów z systemem',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_email',
                'value' => 'jto@adsystem.pl',
                'type' => 'email',
                'label' => 'Email osoby kontaktowej',
                'description' => 'Adres email do kontaktu w sprawie problemów',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_settings');
    }
}