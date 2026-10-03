<?php

// Nach dem Import der Modbus-Vorlage einmal ausführen.
// Das Skript direkt unterhalb der Instanz "ModBus Gerät" ablegen.
// Es trägt die Modbus-Adressen in das Symcon-Feld "Beschreibung" ein.

$modbusID = IPS_GetParent($_IPS['SELF']);

$descriptions = [
    'plug_locking' => 'Modbus Coil 1 | Lesen: FC01 | Schreiben: FC05, Coil 1',
    'coil_2_unknown' => 'Modbus Coil 2 | Lesen: FC01 | Schreiben: FC05, Coil 2',
    'phase_selection' => 'Modbus Coil 3 | Lesen: FC01 | Schreiben: FC05, Coil 3',
    'coil_4_unknown' => 'Modbus Coil 4 | Lesen: FC01 | Schreiben: FC05, Coil 4',
    'front_sensor_boost_request' => 'Modbus Coil 5 | Lesen: FC01 | Schreiben: FC05, Coil 5',
    'keep_plug_locked_after_charging' => 'Modbus Coil 6 | Lesen: FC01 | Schreiben: FC05, Coil 6',
    'coil_7_unknown' => 'Modbus Coil 7 | Lesen: FC01 | Nur Lesen | Schreibversuch FC05: 02 Illegal Data Address | Bei CP=E bleibt TRUE; keine Zustandsänderung, Bedeutung offen',
    'blocking_fault_status' => 'Modbus Coil 8 | Lesen: FC01 | Nur Lesen | FC05: 02 Illegal Data Address | Blinkfehler CP D/E: FALSE | Schützklebefehler mit rotem Dauerlicht: TRUE, verriegelt bis Steuersicherung AUS/EIN',
    'protocol_signature' => 'Holding Register 40001 | PDU-Adresse 0 | Lesen: FC03 | Nur Lesen',
    'register_40002_unknown' => 'Holding Register 40002 | PDU-Adresse 1 | Lesen: FC03 | Nur Lesen',
    'register_40003_unknown' => 'Holding Register 40003 | PDU-Adresse 2 | Lesen: FC03 | Nur Lesen',
    'manufacturer' => 'Holding Register 40004-40019 | PDU-Adressen 3-18 | Lesen: FC03 | Nur Lesen',
    'product_identifier' => 'Holding Register 40020-40035 | PDU-Adressen 19-34 | Lesen: FC03 | Nur Lesen',
    'device_identifier' => 'Holding Register 40036-40051 | PDU-Adressen 35-50 | Lesen: FC03 | Nur Lesen',
    'version_string' => 'Holding Register 40052-40067 | PDU-Adressen 51-66 | Lesen: FC03 | Nur Lesen',
    'max_current_selector' => 'Holding Register 40068 | PDU-Adresse 67 | Lesen: FC03 | Nur Lesen',
    'cable_rated_current' => 'Holding Register 40069 | PDU-Adresse 68 | Lesen: FC03 | Nur Lesen',
    'state_code' => 'Holding Register 40070 | PDU-Adresse 69 | Lesen: FC03 | Nur Lesen',
    'cp_pwm_duty_cycle' => 'Holding Register 40071 | PDU-Adresse 70 | Lesen: FC03 | Nur Lesen',
    'current_l1' => 'Holding Register 40072 | PDU-Adresse 71 | Lesen: FC03 | Nur Lesen | Skalierung: 0,1 A/Digit',
    'current_l2' => 'Holding Register 40073 | PDU-Adresse 72 | Lesen: FC03 | Nur Lesen | Skalierung: 0,1 A/Digit',
    'current_l3' => 'Holding Register 40074 | PDU-Adresse 73 | Lesen: FC03 | Nur Lesen | Skalierung: 0,1 A/Digit',
    'register_40075_unknown' => 'Holding Register 40075 | PDU-Adresse 74 | Lesen: FC03 | Nur Lesen',
    'register_40076_unknown' => 'Holding Register 40076 | PDU-Adresse 75 | Lesen: FC03 | Nur Lesen',
    'register_40077_unknown' => 'Holding Register 40077 | PDU-Adresse 76 | Lesen: FC03 | Nur Lesen',
    'register_40078_unknown' => 'Holding Register 40078 | PDU-Adresse 77 | Lesen: FC03 | Nur Lesen',
    'register_40079_unknown' => 'Holding Register 40079 | PDU-Adresse 78 | Lesen: FC03 | Nur Lesen',
    'register_40080_unknown' => 'Holding Register 40080 | PDU-Adresse 79 | Lesen: FC03 | Nur Lesen',
    'charging_current_setpoint' => 'Holding Register 40081 | PDU-Adresse 80 | Lesen: FC03 | Schreiben: FC06, PDU-Adresse 80',
    'register_40082_unknown' => 'Holding Register 40082 | PDU-Adresse 81 | Lesen: FC03 | Schreiben: FC06, PDU-Adresse 81',
    'solar_mode' => 'Holding Register 40083 | PDU-Adresse 82 | Lesen: FC03 | Schreiben: FC06, PDU-Adresse 82',
    'register_40084_unknown' => 'Holding Register 40084 | PDU-Adresse 83 | Lesen: FC03 | Nur Lesen',
    'fault_code' => 'Holding Register 40085 | PDU-Adresse 84 | Lesen: FC03 | Nur Lesen | Profil E3DC.Wallbox.ErrorCode wird direkt aus der Modbus-Vorlage angelegt',
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
];

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
