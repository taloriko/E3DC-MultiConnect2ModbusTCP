<?php

// Nach dem Import der Modbus-Vorlage einmal ausführen.
// Das Skript direkt unterhalb der Instanz "ModBus Gerät" ablegen.
// Es trägt die Modbus-Adressen in das Symcon-Feld "Beschreibung" ein.

$modbusID = IPS_GetParent($_IPS['SELF']);

$descriptions = [
    'coil_1_steckerverriegelung' => 'Modbus Coil 1 | Lesen: FC01 | Schreiben: FC05, Coil 1',
    'coil_2_unbekannt' => 'Modbus Coil 2 | Lesen: FC01 | Schreiben: FC05, Coil 2',
    'coil_3_phasenwahl' => 'Modbus Coil 3 | Lesen: FC01 | Schreiben: FC05, Coil 3',
    'coil_4_unbekannt' => 'Modbus Coil 4 | Lesen: FC01 | Schreiben: FC05, Coil 4',
    'coil_5_frontsensor_boost_anforderung' => 'Modbus Coil 5 | Lesen: FC01 | Schreiben: FC05, Coil 5',
    'coil_6_stecker_nach_ladeende_verriegelt' => 'Modbus Coil 6 | Lesen: FC01 | Schreiben: FC05, Coil 6',
    'coil_7_ro_unbekannt' => 'Modbus Coil 7 | Lesen: FC01 | Nur Lesen | Schreibversuch FC05: 02 Illegal Data Address',
    'coil_8_ro_unbekannt' => 'Modbus Coil 8 | Lesen: FC01 | Nur Lesen | Schreibversuch FC05: 02 Illegal Data Address | Bei simuliertem Schützklebefehler TRUE | Fehler verriegelt bis Steuersicherung AUS/EIN',
    'kennung' => 'Holding Register 40001 | PDU-Adresse 0 | Lesen: FC03 | Nur Lesen',
    'reg_40002' => 'Holding Register 40002 | PDU-Adresse 1 | Lesen: FC03 | Nur Lesen',
    'reg_40003' => 'Holding Register 40003 | PDU-Adresse 2 | Lesen: FC03 | Nur Lesen',
    'hersteller' => 'Holding Register 40004-40019 | PDU-Adressen 3-18 | Lesen: FC03 | Nur Lesen',
    'produktkennung' => 'Holding Register 40020-40035 | PDU-Adressen 19-34 | Lesen: FC03 | Nur Lesen',
    'geraetekennung' => 'Holding Register 40036-40051 | PDU-Adressen 35-50 | Lesen: FC03 | Nur Lesen',
    'versionsstring' => 'Holding Register 40052-40067 | PDU-Adressen 51-66 | Lesen: FC03 | Nur Lesen',
    'codierschalter' => 'Holding Register 40068 | PDU-Adresse 67 | Lesen: FC03 | Nur Lesen',
    'kabel_nennstrom' => 'Holding Register 40069 | PDU-Adresse 68 | Lesen: FC03 | Nur Lesen',
    'zustandscode' => 'Holding Register 40070 | PDU-Adresse 69 | Lesen: FC03 | Nur Lesen',
    'cp_pwm_duty_cycle' => 'Holding Register 40071 | PDU-Adresse 70 | Lesen: FC03 | Nur Lesen',
    'strom_l1' => 'Holding Register 40072 | PDU-Adresse 71 | Lesen: FC03 | Nur Lesen | Skalierung: 0,1 A/Digit',
    'strom_l2' => 'Holding Register 40073 | PDU-Adresse 72 | Lesen: FC03 | Nur Lesen | Skalierung: 0,1 A/Digit',
    'strom_l3' => 'Holding Register 40074 | PDU-Adresse 73 | Lesen: FC03 | Nur Lesen | Skalierung: 0,1 A/Digit',
    'reg_40075' => 'Holding Register 40075 | PDU-Adresse 74 | Lesen: FC03 | Nur Lesen',
    'reg_40076' => 'Holding Register 40076 | PDU-Adresse 75 | Lesen: FC03 | Nur Lesen',
    'reg_40077' => 'Holding Register 40077 | PDU-Adresse 76 | Lesen: FC03 | Nur Lesen',
    'reg_40078' => 'Holding Register 40078 | PDU-Adresse 77 | Lesen: FC03 | Nur Lesen',
    'reg_40079' => 'Holding Register 40079 | PDU-Adresse 78 | Lesen: FC03 | Nur Lesen',
    'reg_40080' => 'Holding Register 40080 | PDU-Adresse 79 | Lesen: FC03 | Nur Lesen',
    'ladestrom_sollwert' => 'Holding Register 40081 | PDU-Adresse 80 | Lesen: FC03 | Schreiben: FC06, PDU-Adresse 80',
    'steuerregister_40082' => 'Holding Register 40082 | PDU-Adresse 81 | Lesen: FC03 | Schreiben: FC06, PDU-Adresse 81',
    'steuerregister_40083' => 'Holding Register 40083 | PDU-Adresse 82 | Lesen: FC03 | Schreiben: FC06, PDU-Adresse 82',
    'reg_40084' => 'Holding Register 40084 | PDU-Adresse 83 | Lesen: FC03 | Nur Lesen',
    'reg_40085' => 'Holding Register 40085 | PDU-Adresse 84 | Lesen: FC03 | Nur Lesen | Wert 130 bei simuliertem Schützklebefehler | Reset auf 0 erst nach Steuersicherung AUS/EIN',
    'rfid_karten_id' => 'Holding Register 40086-40089 | PDU-Adressen 85-88 | Lesen: FC03 | Nur Lesen',
    'reg_40090' => 'Holding Register 40090 | PDU-Adresse 89 | Lesen: FC03 | Nur Lesen',
    'reg_40091' => 'Holding Register 40091 | PDU-Adresse 90 | Lesen: FC03 | Nur Lesen',
    'reg_40092' => 'Holding Register 40092 | PDU-Adresse 91 | Lesen: FC03 | Nur Lesen',
    'reg_40093' => 'Holding Register 40093 | PDU-Adresse 92 | Lesen: FC03 | Nur Lesen',
    'reg_40094' => 'Holding Register 40094 | PDU-Adresse 93 | Lesen: FC03 | Nur Lesen',
    'reg_40095' => 'Holding Register 40095 | PDU-Adresse 94 | Lesen: FC03 | Nur Lesen',
    'reg_40096' => 'Holding Register 40096 | PDU-Adresse 95 | Lesen: FC03 | Nur Lesen',
    'reg_40097' => 'Holding Register 40097 | PDU-Adresse 96 | Lesen: FC03 | Nur Lesen',
    'reg_40098' => 'Holding Register 40098 | PDU-Adresse 97 | Lesen: FC03 | Nur Lesen',
    'reg_40099' => 'Holding Register 40099 | PDU-Adresse 98 | Lesen: FC03 | Nur Lesen',
    'reg_40100' => 'Holding Register 40100 | PDU-Adresse 99 | Lesen: FC03 | Nur Lesen',
    'reg_40101' => 'Holding Register 40101 | PDU-Adresse 100 | Lesen: FC03 | Nur Lesen',
    'reg_40102' => 'Holding Register 40102 | PDU-Adresse 101 | Lesen: FC03 | Nur Lesen',
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
