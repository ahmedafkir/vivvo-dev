<?php
/* =============================================================================
 * $Revision: 4834 $
 * $Date: 2010-03-30 11:39:23 +0200 (Tue, 30 Mar 2010) $
 *
 * Vivvo CMS v4.5.2r (build 6084)
 *
 * Copyright (c) 2010, Spoonlabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * =============================================================================
 */


define ('VIVVO_DB_COLLATION', 'utf8_unicode_ci');

$lang = array(
	'LNG_INSTALLER_NEED_HELP' => 'Potrebna Vam je pomoć?',
	'LNG_INSTALLER_CHOOSE_LANGUAGE' => 'Odaberite jezik',
	'LNG_INSTALLER_REQUIREMENTS' => 'Zahtevi',
	'LNG_INSTALLER_LICENSE' => 'Licenca',
	'LNG_INSTALLER_DATABASE_CONFIGURATION' => 'Konfiguracija baze',
	'LNG_INSTALLER_DEFAULT_OPTIONS' => 'Opcije',
	'LNG_INSTALLER_ADVANCED_OPTIONS' => 'Napredne opcije (opciono)',
	'LNG_INSTALLER_FINISH' => 'Kraj',
	'LNG_INSTALLER_STEP_1' => 'Korak 1: Odaberite jezik',
	'LNG_INSTALLER_STEP_2' => 'Korak 2: Zahtevi',
	'LNG_INSTALLER_STEP_3' => 'Korak 3: Licenca',
	'LNG_INSTALLER_STEP_4' => 'Korak 4: Konfiguracija baze',
	'LNG_INSTALLER_STEP_5' => 'Korak 5: Opcije',
	'LNG_INSTALLER_STEP_6' => 'Korak 6: Kraj',
	'LNG_INSTALLER_BUTTON_NEXT_STEP' => 'Sledeći korak',
	'LNG_INSTALLER_PHP' => 'PHP verzija',
	'LNG_INSTALLER_MYSQL_VERSION' => 'MySQL verzija',
	'LNG_INSTALLER_REMOTE_COMUNICATION' => 'Udaljena konekcija',
	'LNG_INSTALLER_ZLIB_EXTENSION' => 'Zlib ekstenzija',
	'LNG_INSTALLER_OPTIONAL' => 'Opciono',
	'LNG_INSTALLER_REQUIRED' => 'Obavezno',
	'LNG_INSTALLER_SENDMAIL' => 'Sendmail/SMTP',
	'LNG_INSTALLER_INVALID' => 'Greška',
	'LNG_INSTALLER_OK' => 'OK',
	'LNG_INSTALLER_MOD_REWRITE' => 'Mod Rewrite',
	'LNG_INSTALLER_UNKNOW' => 'Nepoznato',
	'LNG_INSTALLER_GD' => 'GD ekstenzija',
	'LNG_INSTALLER_FILES' => 'Datoteke',
	'LNG_INSTALLER_FILES_CHANGE_PERMISSION' => 'Promenite permise',
	'LNG_INSTALLER_FIX_FILES_FIRST' => 'Ispravite datoteke prvo',
	'LNG_INSTALLER_I_AGREE' => 'Slažem se',
	'LNG_INSTALLER_YOU_MUST_ACCEPT' => 'Morate prihvatiti uslove!',
	'LNG_INSTALLER_URL' => 'Sajt (URL)',
	'LNG_INSTALLER_HOST' => 'Baza - Host',
	'LNG_INSTALLER_DATABASE' => 'Baza - Ime',
	'LNG_INSTALLER_USER' => 'Baza - Korisničko ime',
	'LNG_INSTALLER_PASSWORD' => 'Baza - Šifra',
	'LNG_INSTALLER_TBL_PREFIX' => 'Baza - Prefiks tabele (bez razmaka, samo Engleski alfabet, brojevi i _)',
	'LNG_INSTALLER_WEB_SITE_TITLE' => 'Naslov sajta',
	'LNG_INSTALLER_ADMINISTRATOR_EMAIL' => 'E-mail administratora',
	'LNG_INSTALLER_ADMINISTRATOR_USERNAME' => 'Korisnicko ime administratora',
	'LNG_INSTALLER_ADMINISTRATOR_PASSWORD' => 'Šifra',
	'LNG_INSTALLER_ADMINISTRATOR_PASSWORD_STRENGHT' => 'Sigurnost lozinke',
	'LNG_INSTALLER_ADMINISTRATOR_RETYPE_PASSWORD' => 'Ponovi šifru',
	'LNG_INSTALLER_VIVVO_ADMIN_CONTROL_PANEL' => 'Vivvo Administracioni panel',
	'LNG_INSTALLER_VIVVO_VIEW_WEBSITE' => 'Pogledaj sajt',
	'LNG_INSTALLER_VIVVO_READ_MANUAL' => 'Pročitaj uputstvo',
	'LNG_INSTALLER_CANT_CONNECT_TO_DATABASE' => 'Ne mogu da se konektujem na bazu',
	'LNG_INSTALLER_CANT_SELECT_DATABASE' => 'Ne mogu da odaberem bazu',
	'LNG_INSTALLER_CANT_CREATE_CONFIG_FILE' => 'config datoteka ne može biti kreirana',
	'LNG_INSTALLER_PASSWORD_AND_RETYPE_PASSWORD_MUST_BE_SAME' => 'Šifra i potvrda šifre moraju biti identične',
	'LNG_INSTALLER_PASSWORD_MINIMUM_6_CHAR' => 'Šifra mora sadržati najmanje 6 karaktera',
	'LNG_INSTALLER_WRONG_USERNAME' => 'Pogrešno administratorsko korisničko ime',
	'LNG_INSTALLER_WRONG_EMAIL' => 'Pogrešan e-mail',
	'LNG_INSTALLER_THANK_YOU_MESSAGE' => 'Hvala vam što ste instalirali naš proizvod',
	'LNG_INSTALLER_MISSING_DATA' => 'Nedostaju informacije: Ime baze, korisničko ime baze ili lozinka za bazu',
	'LNG_INSTALLER_CREATE_FILE' => 'Kreiraj fajl',
	'LNG_INSTALLER_CREATE_FILE_ERROR_MESSAGE' => 'Ne možete nastaviti instalaciju pošto vaš server ne omogućuje kreiranje novih datoteka.',
	'LNG_INSTALLER_INFO_STEP_1' => 'Izaberite jezik za proces instalacije iz padajućeg menija. Taj jezik će, ukoliko je moguće, biti inicijalno izabran kao predefinisani jezik za sajt.',
	'LNG_INSTALLER_INFO_STEP_2' => 'Ukoliko je bilo koji zahtev označen crvenom bojom molimo vas da preduzmete potrebne akcije. Ukoliko to ne učinite instalacija neće raditi ispravno.',
	'LNG_INSTALLER_INFO_STEP_4' => 'Da biste postavili Vivvo bazu, molimo unesite sledeće podatke.',
	'LNG_INSTALLER_INFO_STEP_5' => 'Molimo unesite naslov sajta i kreirajte prvi korisnički nalog. Taj nalog će biti administratorski nalog koji se koristi za pristup administracionom panelu.'
);
?>