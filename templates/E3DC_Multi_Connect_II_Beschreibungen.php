<?php

// Nach dem Import der Modbus-Vorlage einmal ausführen.
// Das Skript direkt unterhalb der Instanz "ModBus Gerät" ablegen.
// Es trägt die Modbus-Adressen in das Symcon-Feld "Beschreibung" ein.

$modbusID = IPS_GetParent($_IPS['SELF']);

$descriptions = [
    'coil_0_unknown' => 'Modbus Coil 0 | Lesen: FC01 | Schreiben FC05 auf Adresse 0 getestet: 02 Illegal Data Address | daher nur Lesen | Bedeutung unbekannt',
    'plug_locking' => 'Modbus Coil 1 | Lesen: FC01 | Schreiben: FC05, Coil 1',
    'coil_2_unknown' => 'Modbus Coil 2 | Lesen: FC01 | Schreiben: FC05, Coil 2',
    'phase_selection' => 'Modbus Coil 3 | Lesen: FC01 | Schreiben: FC05, Coil 3',
    'coil_4_unknown' => 'Modbus Coil 4 | Lesen: FC01 | Schreiben: FC05, Coil 4 | Bedeutung unbekannt | fällt selbstständig etwa alle 60 s auf FALSE zurück',
    'front_sensor_boost_request' => 'Modbus Coil 5 | Lesen: FC01 | Schreiben: FC05, Coil 5',
    'keep_plug_locked_after_charging' => 'Modbus Coil 6 | Lesen: FC01 | Schreiben: FC05, Coil 6',
    'coil_7_unknown' => 'Modbus Coil 7 | Lesen: FC01 | Nur Lesen | Schreibversuch FC05: 02 Illegal Data Address | Bei CP=E bleibt TRUE; keine Zustandsänderung, Bedeutung offen',
    'blocking_fault_status' => 'Modbus Coil 8 | Lesen: FC01 | Nur Lesen | FC05: 02 Illegal Data Address | Blinkfehler CP D/E: FALSE | Fehlercode 128 bei abgezogenem Front-Flachbandkabel: TRUE | Fehlercode 130 Schützklebefehler: TRUE, dort verriegelt bis Steuersicherung AUS/EIN',
    'protocol_signature' => 'Holding Register 40001 | PDU-Adresse 0 | Lesen: FC03 | Nur Lesen',
    'register_40002_unknown' => 'Holding Register 40002 | PDU-Adresse 1 | Lesen: FC03 | Nur Lesen',
    'register_40003_unknown' => 'Holding Register 40003 | PDU-Adresse 2 | Lesen: FC03 | Nur Lesen',
    'manufacturer' => 'Holding Register 40004-40019 | PDU-Adressen 3-18 | Lesen: FC03 | Nur Lesen',
    'product_identifier' => 'Holding Register 40020-40035 | PDU-Adressen 19-34 | Lesen: FC03 | Nur Lesen',
    'device_identifier' => 'Holding Register 40036-40051 | PDU-Adressen 35-50 | Lesen: FC03 | Nur Lesen',
    'version_string' => 'Holding Register 40052-40067 | PDU-Adressen 51-66 | Lesen: FC03 | Nur Lesen',
    'max_current_selector' => 'Holding Register 40068 | PDU-Adresse 67 | Lesen: FC03 | Nur Lesen | Profil E3DC.Wallbox.CurrentSelector | Anzeige: 10/13/16/20/25/32 A sowie A/B/C',
    'cable_rated_current' => 'Holding Register 40069 | PDU-Adresse 68 | Lesen: FC03 | Nur Lesen | Profil ~Ampere',
    'state_code' => 'Holding Register 40070 | PDU-Adresse 69 | Lesen: FC03 | Nur Lesen',
    'cp_pwm_duty_cycle' => 'Holding Register 40071 | PDU-Adresse 70 | Lesen: FC03 | Nur Lesen | Profil ~Intensity.100 (%)',
    'current_l1' => 'Holding Register 40072 | PDU-Adresse 71 | Lesen: FC03 | Nur Lesen | Skalierung: 0,1 A/Digit | Profil ~Ampere',
    'current_l2' => 'Holding Register 40073 | PDU-Adresse 72 | Lesen: FC03 | Nur Lesen | Skalierung: 0,1 A/Digit | Profil ~Ampere',
    'current_l3' => 'Holding Register 40074 | PDU-Adresse 73 | Lesen: FC03 | Nur Lesen | Skalierung: 0,1 A/Digit | Profil ~Ampere',
    'register_40075_unknown' => 'Holding Register 40075 | PDU-Adresse 74 | Lesen: FC03 | Nur Lesen',
    'register_40076_unknown' => 'Holding Register 40076 | PDU-Adresse 75 | Lesen: FC03 | Nur Lesen',
    'register_40077_unknown' => 'Holding Register 40077 | PDU-Adresse 76 | Lesen: FC03 | Nur Lesen',
    'register_40078_unknown' => 'Holding Register 40078 | PDU-Adresse 77 | Lesen: FC03 | Nur Lesen',
    'register_40079_unknown' => 'Holding Register 40079 | PDU-Adresse 78 | Lesen: FC03 | Nur Lesen',
    'register_40080_unknown' => 'Holding Register 40080 | PDU-Adresse 79 | Lesen: FC03 | Nur Lesen',
    'charging_current_setpoint' => 'Holding Register 40081 | PDU-Adresse 80 | Lesen: FC03 | Schreiben: FC06, PDU-Adresse 80 | Profil ~Ampere',
    'register_40082_unknown' => 'Holding Register 40082 | PDU-Adresse 81 | Lesen: FC03 | Schreiben: FC06, PDU-Adresse 81',
    'solar_mode' => 'Holding Register 40083 | PDU-Adresse 82 | Lesen: FC03 | Schreiben: FC06, PDU-Adresse 82',
    'register_40084_unknown' => 'Holding Register 40084 | PDU-Adresse 83 | Lesen: FC03 | Nur Lesen',
    'fault_code' => 'Holding Register 40085 | PDU-Adresse 84 | Lesen: FC03 | Nur Lesen | Profil E3DC.Wallbox.ErrorCode | 128 getestet: Front-Flachbandkabel nicht gesteckt | 129 getestet: Codierschalter unter Spannung auf A/C verstellt, verriegelnd | 130 getestet: Schützklebefehler',
    'rfid_card_id' => 'Holding Register 40086-40089 | PDU-Adressen 85-88 | Lesen: FC03 | Nur Lesen',
    'register_40090_unknown' => 'Holding Register 40090 | PDU-Adresse 89 | Lesen: FC03 | Nur Lesen',
    'register_40091_unknown' => 'Holding Register 40091 | PDU-Adresse 90 | Lesen: FC03 | Nur Lesen',
    'register_40092_unknown' => 'Holding Register 40092 | PDU-Adresse 91 | Lesen: FC03 | Nur Lesen',
    'register_40093_unknown' => 'Holding Register 40093 | PDU-Adresse 92 | Lesen: FC03 | Nur Lesen',
    'register_40094_unknown' => 'Holding Register 40094 | PDU-Adresse 93 | Lesen: FC03 | Nur Lesen',
    'register_40095_unknown' => 'Holding Register 40095 | PDU-Adresse 94 | Lesen: FC03 | Nur Lesen',
    'register_40096_unknown' => 'Holding Register 40096 | PDU-Adresse 95 | Lesen: FC03 | Nur Lesen',
    'register_40097_unknown' => 'Holding Register 40097 | PDU-Adresse 96 | Lesen: FC03 | Nur Lesen',
    'register_40098_unknown' => 'Holding Register 40098 | PDU-Adresse 97 | Lesen: FC03 | Nur Lesen',
    'register_40099_unknown' => 'Holding Register 40099 | PDU-Adresse 98 | Lesen: FC03 | Nur Lesen',
    'register_40100_unknown' => 'Holding Register 40100 | PDU-Adresse 99 | Lesen: FC03 | Nur Lesen',
    'register_40101_unknown' => 'Holding Register 40101 | PDU-Adresse 100 | Lesen: FC03 | Nur Lesen',
    'register_40102_unknown' => 'Holding Register 40102 | PDU-Adresse 101 | Lesen: FC03 | Nur Lesen',
    'register_50000_unknown' => 'Holding Register 50000 | PDU-Adresse 9999 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50001_unknown' => 'Holding Register 50001 | PDU-Adresse 10000 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50002_unknown' => 'Holding Register 50002 | PDU-Adresse 10001 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50003_unknown' => 'Holding Register 50003 | PDU-Adresse 10002 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50004_unknown' => 'Holding Register 50004 | PDU-Adresse 10003 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50005_unknown' => 'Holding Register 50005 | PDU-Adresse 10004 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50006_unknown' => 'Holding Register 50006 | PDU-Adresse 10005 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50007_unknown' => 'Holding Register 50007 | PDU-Adresse 10006 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50008_unknown' => 'Holding Register 50008 | PDU-Adresse 10007 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50009_unknown' => 'Holding Register 50009 | PDU-Adresse 10008 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50021_unknown' => 'Holding Register 50021 | PDU-Adresse 10020 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50022_unknown' => 'Holding Register 50022 | PDU-Adresse 10021 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50025_unknown' => 'Holding Register 50025 | PDU-Adresse 10024 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50026_unknown' => 'Holding Register 50026 | PDU-Adresse 10025 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50029_unknown' => 'Holding Register 50029 | PDU-Adresse 10028 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50030_unknown' => 'Holding Register 50030 | PDU-Adresse 10029 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50031_unknown' => 'Holding Register 50031 | PDU-Adresse 10030 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_50032_unknown' => 'Holding Register 50032 | PDU-Adresse 10031 | Lesen: FC03 | Nur Lesen in der Vorlage | Bedeutung noch nicht verifiziert',
    'register_55000_unknown' => 'Holding Register 55000 | PDU-Adresse 14999 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55001_unknown' => 'Holding Register 55001 | PDU-Adresse 15000 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt | Scanwert: 8',
    'register_55002_unknown' => 'Holding Register 55002 | PDU-Adresse 15001 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55003_unknown' => 'Holding Register 55003 | PDU-Adresse 15002 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55004_unknown' => 'Holding Register 55004 | PDU-Adresse 15003 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55005_unknown' => 'Holding Register 55005 | PDU-Adresse 15004 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55006_unknown' => 'Holding Register 55006 | PDU-Adresse 15005 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55007_unknown' => 'Holding Register 55007 | PDU-Adresse 15006 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55008_unknown' => 'Holding Register 55008 | PDU-Adresse 15007 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55009_unknown' => 'Holding Register 55009 | PDU-Adresse 15008 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55010_unknown' => 'Holding Register 55010 | PDU-Adresse 15009 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55011_unknown' => 'Holding Register 55011 | PDU-Adresse 15010 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55012_unknown' => 'Holding Register 55012 | PDU-Adresse 15011 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55013_unknown' => 'Holding Register 55013 | PDU-Adresse 15012 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55014_unknown' => 'Holding Register 55014 | PDU-Adresse 15013 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55015_unknown' => 'Holding Register 55015 | PDU-Adresse 15014 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55016_unknown' => 'Holding Register 55016 | PDU-Adresse 15015 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55017_unknown' => 'Holding Register 55017 | PDU-Adresse 15016 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55018_unknown' => 'Holding Register 55018 | PDU-Adresse 15017 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55019_unknown' => 'Holding Register 55019 | PDU-Adresse 15018 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55020_unknown' => 'Holding Register 55020 | PDU-Adresse 15019 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55021_unknown' => 'Holding Register 55021 | PDU-Adresse 15020 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55022_unknown' => 'Holding Register 55022 | PDU-Adresse 15021 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55023_unknown' => 'Holding Register 55023 | PDU-Adresse 15022 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55024_unknown' => 'Holding Register 55024 | PDU-Adresse 15023 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55025_unknown' => 'Holding Register 55025 | PDU-Adresse 15024 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55026_unknown' => 'Holding Register 55026 | PDU-Adresse 15025 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55027_unknown' => 'Holding Register 55027 | PDU-Adresse 15026 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55028_unknown' => 'Holding Register 55028 | PDU-Adresse 15027 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55029_unknown' => 'Holding Register 55029 | PDU-Adresse 15028 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55030_unknown' => 'Holding Register 55030 | PDU-Adresse 15029 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55031_unknown' => 'Holding Register 55031 | PDU-Adresse 15030 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55032_unknown' => 'Holding Register 55032 | PDU-Adresse 15031 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55033_unknown' => 'Holding Register 55033 | PDU-Adresse 15032 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55034_unknown' => 'Holding Register 55034 | PDU-Adresse 15033 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55035_unknown' => 'Holding Register 55035 | PDU-Adresse 15034 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55036_unknown' => 'Holding Register 55036 | PDU-Adresse 15035 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55037_unknown' => 'Holding Register 55037 | PDU-Adresse 15036 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55038_unknown' => 'Holding Register 55038 | PDU-Adresse 15037 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55039_unknown' => 'Holding Register 55039 | PDU-Adresse 15038 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55040_unknown' => 'Holding Register 55040 | PDU-Adresse 15039 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55041_unknown' => 'Holding Register 55041 | PDU-Adresse 15040 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55042_unknown' => 'Holding Register 55042 | PDU-Adresse 15041 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55043_unknown' => 'Holding Register 55043 | PDU-Adresse 15042 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55044_unknown' => 'Holding Register 55044 | PDU-Adresse 15043 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55045_unknown' => 'Holding Register 55045 | PDU-Adresse 15044 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55046_unknown' => 'Holding Register 55046 | PDU-Adresse 15045 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55047_unknown' => 'Holding Register 55047 | PDU-Adresse 15046 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55048_unknown' => 'Holding Register 55048 | PDU-Adresse 15047 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55049_unknown' => 'Holding Register 55049 | PDU-Adresse 15048 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55050_unknown' => 'Holding Register 55050 | PDU-Adresse 15049 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55051_unknown' => 'Holding Register 55051 | PDU-Adresse 15050 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55052_unknown' => 'Holding Register 55052 | PDU-Adresse 15051 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55053_unknown' => 'Holding Register 55053 | PDU-Adresse 15052 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55054_unknown' => 'Holding Register 55054 | PDU-Adresse 15053 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55055_unknown' => 'Holding Register 55055 | PDU-Adresse 15054 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55056_unknown' => 'Holding Register 55056 | PDU-Adresse 15055 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55057_unknown' => 'Holding Register 55057 | PDU-Adresse 15056 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55058_unknown' => 'Holding Register 55058 | PDU-Adresse 15057 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55059_unknown' => 'Holding Register 55059 | PDU-Adresse 15058 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55060_unknown' => 'Holding Register 55060 | PDU-Adresse 15059 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55061_unknown' => 'Holding Register 55061 | PDU-Adresse 15060 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55062_unknown' => 'Holding Register 55062 | PDU-Adresse 15061 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55063_unknown' => 'Holding Register 55063 | PDU-Adresse 15062 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55064_unknown' => 'Holding Register 55064 | PDU-Adresse 15063 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55065_unknown' => 'Holding Register 55065 | PDU-Adresse 15064 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55066_unknown' => 'Holding Register 55066 | PDU-Adresse 15065 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55067_unknown' => 'Holding Register 55067 | PDU-Adresse 15066 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55068_unknown' => 'Holding Register 55068 | PDU-Adresse 15067 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55069_unknown' => 'Holding Register 55069 | PDU-Adresse 15068 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55070_unknown' => 'Holding Register 55070 | PDU-Adresse 15069 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55071_unknown' => 'Holding Register 55071 | PDU-Adresse 15070 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55072_unknown' => 'Holding Register 55072 | PDU-Adresse 15071 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55073_unknown' => 'Holding Register 55073 | PDU-Adresse 15072 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55074_unknown' => 'Holding Register 55074 | PDU-Adresse 15073 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55075_unknown' => 'Holding Register 55075 | PDU-Adresse 15074 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55076_unknown' => 'Holding Register 55076 | PDU-Adresse 15075 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55077_unknown' => 'Holding Register 55077 | PDU-Adresse 15076 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55078_unknown' => 'Holding Register 55078 | PDU-Adresse 15077 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55079_unknown' => 'Holding Register 55079 | PDU-Adresse 15078 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55080_unknown' => 'Holding Register 55080 | PDU-Adresse 15079 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55081_unknown' => 'Holding Register 55081 | PDU-Adresse 15080 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55082_unknown' => 'Holding Register 55082 | PDU-Adresse 15081 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55083_unknown' => 'Holding Register 55083 | PDU-Adresse 15082 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55084_unknown' => 'Holding Register 55084 | PDU-Adresse 15083 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55085_unknown' => 'Holding Register 55085 | PDU-Adresse 15084 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55086_unknown' => 'Holding Register 55086 | PDU-Adresse 15085 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55087_unknown' => 'Holding Register 55087 | PDU-Adresse 15086 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55088_unknown' => 'Holding Register 55088 | PDU-Adresse 15087 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55089_unknown' => 'Holding Register 55089 | PDU-Adresse 15088 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55090_unknown' => 'Holding Register 55090 | PDU-Adresse 15089 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55091_unknown' => 'Holding Register 55091 | PDU-Adresse 15090 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55092_unknown' => 'Holding Register 55092 | PDU-Adresse 15091 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55093_unknown' => 'Holding Register 55093 | PDU-Adresse 15092 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55094_unknown' => 'Holding Register 55094 | PDU-Adresse 15093 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55095_unknown' => 'Holding Register 55095 | PDU-Adresse 15094 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55096_unknown' => 'Holding Register 55096 | PDU-Adresse 15095 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55097_unknown' => 'Holding Register 55097 | PDU-Adresse 15096 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55098_unknown' => 'Holding Register 55098 | PDU-Adresse 15097 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55099_unknown' => 'Holding Register 55099 | PDU-Adresse 15098 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55100_unknown' => 'Holding Register 55100 | PDU-Adresse 15099 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55101_unknown' => 'Holding Register 55101 | PDU-Adresse 15100 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55102_unknown' => 'Holding Register 55102 | PDU-Adresse 15101 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55103_unknown' => 'Holding Register 55103 | PDU-Adresse 15102 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55104_unknown' => 'Holding Register 55104 | PDU-Adresse 15103 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55105_unknown' => 'Holding Register 55105 | PDU-Adresse 15104 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55106_unknown' => 'Holding Register 55106 | PDU-Adresse 15105 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55107_unknown' => 'Holding Register 55107 | PDU-Adresse 15106 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55108_unknown' => 'Holding Register 55108 | PDU-Adresse 15107 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55109_unknown' => 'Holding Register 55109 | PDU-Adresse 15108 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55110_unknown' => 'Holding Register 55110 | PDU-Adresse 15109 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55111_unknown' => 'Holding Register 55111 | PDU-Adresse 15110 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55112_unknown' => 'Holding Register 55112 | PDU-Adresse 15111 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55113_unknown' => 'Holding Register 55113 | PDU-Adresse 15112 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55114_unknown' => 'Holding Register 55114 | PDU-Adresse 15113 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55115_unknown' => 'Holding Register 55115 | PDU-Adresse 15114 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55116_unknown' => 'Holding Register 55116 | PDU-Adresse 15115 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55117_unknown' => 'Holding Register 55117 | PDU-Adresse 15116 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55118_unknown' => 'Holding Register 55118 | PDU-Adresse 15117 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55119_unknown' => 'Holding Register 55119 | PDU-Adresse 15118 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55120_unknown' => 'Holding Register 55120 | PDU-Adresse 15119 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55121_unknown' => 'Holding Register 55121 | PDU-Adresse 15120 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55122_unknown' => 'Holding Register 55122 | PDU-Adresse 15121 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55123_unknown' => 'Holding Register 55123 | PDU-Adresse 15122 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55124_unknown' => 'Holding Register 55124 | PDU-Adresse 15123 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55125_unknown' => 'Holding Register 55125 | PDU-Adresse 15124 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'register_55126_unknown' => 'Holding Register 55126 | PDU-Adresse 15125 | Lesen: FC03 | Schreibbarkeit nicht getestet | Bedeutung unbekannt',
    'mac_address_raw' => 'Holding Register 50010-50012 | 6 Byte STRING (HEX) | Rohquelle für MAC-Adresse | nur Lesen',
    'ip_address_raw' => 'Holding Register 50013-50014 | 4 Byte STRING (HEX) | Rohquelle für IP-Adresse | nur Lesen',
    'gateway_raw' => 'Holding Register 50015-50016 | 4 Byte STRING (HEX) | Rohquelle für Gateway | nur Lesen',
    'subnet_mask_raw' => 'Holding Register 50017-50018 | 4 Byte STRING (HEX) | Rohquelle für Subnetzmaske | nur Lesen',
    'network_parameter_1_raw' => 'Holding Register 50019-50020 | 4 Byte STRING (HEX) | Rohquelle Netzwerkparameter 1 | genaue Funktion offen | nur Lesen',
    'secondary_ip_address_raw' => 'Holding Register 50023-50024 | 4 Byte STRING (HEX) | Rohquelle zweite IP-Adresse | genaue Rolle offen | nur Lesen',
    'secondary_subnet_mask_raw' => 'Holding Register 50027-50028 | 4 Byte STRING (HEX) | Rohquelle zweite Subnetzmaske | genaue Rolle offen | nur Lesen',
];


// -----------------------------------------------------------------------------
// Lesbare Netzwerkvariablen
// -----------------------------------------------------------------------------

function E3DC_RawHex($variableID)
{
    if ($variableID === false) {
        return '';
    }

    $raw = strtoupper((string) GetValue($variableID));
    return preg_replace('/[^0-9A-F]/', '', $raw);
}

function E3DC_FormatIPv4($raw)
{
    if (strlen($raw) < 8) {
        return $raw;
    }

    $raw = substr($raw, 0, 8);

    return
        hexdec(substr($raw, 0, 2)) . '.' .
        hexdec(substr($raw, 2, 2)) . '.' .
        hexdec(substr($raw, 4, 2)) . '.' .
        hexdec(substr($raw, 6, 2));
}

function E3DC_FormatMAC($raw)
{
    if (strlen($raw) < 12) {
        return $raw;
    }

    $raw = substr($raw, 0, 12);

    return
        substr($raw, 0, 2) . ':' .
        substr($raw, 2, 2) . ':' .
        substr($raw, 4, 2) . ':' .
        substr($raw, 6, 2) . ':' .
        substr($raw, 8, 2) . ':' .
        substr($raw, 10, 2);
}

function E3DC_GetOrCreateStringVariable($parentID, $ident, $name)
{
    $id = @IPS_GetObjectIDByIdent($ident, $parentID);

    if ($id === false) {
        $id = IPS_CreateVariable(3);
        IPS_SetParent($id, $parentID);
        IPS_SetIdent($id, $ident);
    }

    IPS_SetName($id, $name);
    IPS_SetHidden($id, false);

    return $id;
}

function E3DC_UpdateNetworkStrings($modbusID)
{
    $map = [
        'mac_address_raw' => [
            'Ident' => 'mac_address',
            'Name' => 'MAC-Adresse',
            'Type' => 'MAC'
        ],
        'ip_address_raw' => [
            'Ident' => 'ip_address',
            'Name' => 'IP-Adresse',
            'Type' => 'IPv4'
        ],
        'gateway_raw' => [
            'Ident' => 'gateway',
            'Name' => 'Gateway',
            'Type' => 'IPv4'
        ],
        'subnet_mask_raw' => [
            'Ident' => 'subnet_mask',
            'Name' => 'Subnetzmaske',
            'Type' => 'IPv4'
        ],
        'network_parameter_1_raw' => [
            'Ident' => 'network_parameter_1',
            'Name' => 'Netzwerkparameter 1',
            'Type' => 'IPv4'
        ],
        'secondary_ip_address_raw' => [
            'Ident' => 'secondary_ip_address',
            'Name' => 'Zweite IP-Adresse',
            'Type' => 'IPv4'
        ],
        'secondary_subnet_mask_raw' => [
            'Ident' => 'secondary_subnet_mask',
            'Name' => 'Zweite Subnetzmaske',
            'Type' => 'IPv4'
        ]
    ];

    foreach ($map as $rawIdent => $config) {
        $rawID = @IPS_GetObjectIDByIdent($rawIdent, $modbusID);
        if ($rawID === false) {
            continue;
        }

        $raw = E3DC_RawHex($rawID);

        if ($config['Type'] === 'MAC') {
            $value = E3DC_FormatMAC($raw);
        } else {
            $value = E3DC_FormatIPv4($raw);
        }

        $targetID = E3DC_GetOrCreateStringVariable(
            $modbusID,
            $config['Ident'],
            $config['Name']
        );

        SetValueString($targetID, $value);

        // Die Modbus-Rohquelle bleibt aktiv, wird aber im Objektbaum ausgeblendet.
        IPS_SetHidden($rawID, true);
    }
}

// Bei jedem Aufruf die lesbaren Netzwerkstrings aktualisieren.
E3DC_UpdateNetworkStrings($modbusID);

// Beim Timerlauf sind keine weiteren Arbeiten erforderlich.
if ($_IPS['SENDER'] === 'TimerEvent') {
    return;
}

$ok = 0;
$missing = [];

foreach ($descriptions as $ident => $description) {
    $variableID = @IPS_GetObjectIDByIdent($ident, $modbusID);
    if ($variableID === false) {
        $missing[] = $ident;
        continue;
    }

    IPS_SetInfo($variableID, $description);
    $ok++;
}

echo "Beschreibungen gesetzt: " . $ok . PHP_EOL;
if (count($missing) > 0) {
    echo "Nicht gefunden: " . implode(', ', $missing) . PHP_EOL;
}

// Netzwerkdarstellung automatisch aktuell halten.
IPS_SetScriptTimer($_IPS['SELF'], 1);
echo "Lesbare Netzwerkvariablen erstellt/aktualisiert." . PHP_EOL;
echo "Netzwerk-Rohwerte ausgeblendet." . PHP_EOL;
