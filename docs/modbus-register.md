# Direkte Modbus-TCP-Register und Funktionen

Diese Seite enthält ausschließlich die **technische Zuordnung der gefundenen Modbus-Adressen**, deren Bedeutung sowie die dazu durchgeführten Funktionstests.

Testaufbau, Hardware, Kommunikationsparameter und vollständige Adressscans sind in der [README](../README.md) beschrieben.

**Messstand: 02.10.2026**

---

## Definitionen

### Adressdarstellung

Für Holding Register wird auf dieser Seite die klassische **40001-Darstellung** verwendet.

Modbus-PDU und IP-Symcon arbeiten 0-basiert:

| PDU / IP-Symcon | Dokumentation |
|---:|---:|
| 0 | 40001 |
| 80 | 40081 |
| 82 | 40083 |
| 101 | 40102 |

**40000 wird nicht als eigenes Register geführt.**  
PDU-Adresse 0 entspricht hier **40001**.

### Statuskennzeichnung

- **Verifiziert** – am eigenen Gerät reproduzierbar getestet
- **Hypothese** – plausibel, aber Gegenprobe fehlt
- **Fremdquelle** – Bedeutung stammt aus einer externen Quelle
- **RO** – lesbar, Schreiben wurde abgewiesen bzw. ist nicht vorgesehen
- **RW** – Lesen und Schreiben wurden praktisch bestätigt

### ASCII-Codierung

Mehrere Registerbereiche verwenden **zwei ASCII-Zeichen pro 16-Bit-Register**.

Beispiel:

- Dezimalwert: **17202**
- Hex: **0x4332**
- High-Byte: **0x43** → `C`
- Low-Byte: **0x32** → `2`
- Ergebnis: **"C2"**

```text
Register 0x4142  ->  "AB"
         ^  ^
         |  +---- Low-Byte  = zweites Zeichen
         +------- High-Byte = erstes Zeichen
```

Nullbytes dienen als Stringende bzw. Padding:

```text
0x4600 -> "F\0"
0x5200 -> "R\0"
0x0000 -> zwei NUL-Bytes / Padding
```

Zusammenhängende Textfelder werden registerweise zusammengesetzt.

Beispiel Hersteller:

```text
40004 = 0x4841 -> "HA"
40005 = 0x4745 -> "GE"
40006 = 0x5200 -> "R\0"

Ergebnis: "HAGER"
```

Beispiel RFID:

```text
40086 = 0x4330 -> "C0"
40087 = 0x3844 -> "8D"
40088 = 0x3632 -> "62"
40089 = 0x4334 -> "C4"

Ergebnis: "C08D62C4"
```

---

# FC01 – Coils

Die Adressen **1–8** sind lesbar. Der vollständige Scan ist in der [README](../README.md#durchgeführte-adressscans) dokumentiert.

## Übersicht

| Coil | Zugriff | Bedeutung | Test / Verhalten | Status |
|---:|---|---|---|---|
| **1** | **RW** | **Steckerverriegelung / Verriegelungsfreigabe** | 1 → 0 gesetzt: Stecker wird nicht mehr verriegelt. Gleichzeitig geht Discrete Input 1 auf 0. | **Verifiziert** |
| **2** | **RW** | Unbekannt | Auf 0 gesetzt: Laden weiterhin möglich; beide Leistungsschütze ziehen an. Damit im getesteten Zustand weder Ladefreigabe noch 1P-Auswahl. | Bedeutung offen |
| **3** | **RW** | **Phasenwahl** | 0 = 3-phasig, 1 = 1-phasig. Wirkung wird beim nächsten Ladebeginn übernommen. | **Verifiziert** |
| **4** | R | Unbekannt | noch keine Funktion zugeordnet | offen |
| **5** | **RW** | **Boost-Anforderung über Frontsensor** | Frontsensor 1–4 s betätigt → Coil 5 wird TRUE. Erneutes Betätigen setzt ihn nicht zurück. Nach Modbus-Schreiben auf FALSE kann der Sensor ihn erneut auf TRUE setzen. | **Verifiziertes Verhalten; Rücksetzlogik offen** |
| **6** | **RW** | **Stecker nach Ladeende verriegelt lassen** | Coil 6 = 0 → beim Wechsel von CP B auf CP A wird der Stecker entriegelt. Coil 6 = 1 → beim gleichen Wechsel bleibt der Stecker verriegelt. | **Verifiziert** |
| **7** | **RO** | Unbekannt | Lesen möglich; FC05-Schreibversuch → **02 Illegal Data Address** | Schreibzugriff abgewiesen |
| **8** | **RO** | Unbekannt | Lesen möglich; FC05-Schreibversuch → **02 Illegal Data Address** | Schreibzugriff abgewiesen |

## Coil 1 – Steckerverriegelung

Test:

- Ausgangszustand Coil 1 = 1
- Coil 1 auf 0 gesetzt
- der Stecker wird anschließend **nicht mehr verriegelt**
- gleichzeitig wechselt **Discrete Input 1 auf 0**

Damit besteht ein reproduzierbarer Zusammenhang zwischen Coil 1 und der Verriegelungsfunktion.

## Coil 2 – Bedeutung offen

Test:

- Coil 2 auf 0 gesetzt
- Laden bleibt möglich
- beide Leistungsschütze ziehen an

Damit ist Coil 2 im getesteten Zustand **weder die allgemeine Ladefreigabe noch die 1P/3P-Auswahl**.

## Coil 3 – 1P/3P-Phasenwahl

Verifiziertes Verhalten:

- **Coil 3 = 0** → 3-phasiger Betrieb
- **Coil 3 = 1** → 1-phasiger Betrieb
- bei 1-phasigem Start zieht nur der entsprechende Leistungspfad an
- nur **L1** wird durchgeschaltet
- L2/L3 bleiben aus

### Umschalten während des Ladens

Die Änderung wird während eines bereits laufenden Ladevorgangs **nicht unmittelbar übernommen**.

Beobachtung:

1. Ladevorgang läuft 3-phasig.
2. Coil 3 wird auf 1 gesetzt.
3. Der laufende Ladezustand bleibt unverändert.

Für die praktische Regelung ergibt sich deshalb:

```text
Laden stoppen
→ Coil 3 auf gewünschte Phasenart setzen
→ Laden wieder starten
```

Damit ist Coil 3 der bisher gefundene direkte Modbus-Befehl für die **1P/3P-Umschaltung**.


## Coil 5 – Frontsensor

Eigener Test:

- Frontsensor ca. **1–4 Sekunden** betätigen → Coil 5 wird **TRUE**
- erneutes Betätigen → Coil 5 bleibt **TRUE**
- Coil 5 per Modbus auf **FALSE** setzen
- Frontsensor erneut betätigen → Coil 5 wird wieder **TRUE**

Damit ist das Verhalten des Frontsensors auf Coil 5 verifiziert. Die Rücksetzlogik ist noch offen. Laut Hager-Anleitung dient der Kontakt-Sensor bei Solaroptimierung zum Beschleunigen des Ladevorgangs.

## Coil 6 – Stecker nach Ladeende verriegelt lassen

Coil 6 legt fest, ob der Stecker nach Ende der Verbindung bzw. beim Übergang von **CP B** auf **CP A** verriegelt bleibt.

Verifiziertes Verhalten:

- **Coil 6 = 0** → Wechsel von CP B auf CP A → **Stecker wird entriegelt**
- **Coil 6 = 1** → Wechsel von CP B auf CP A → **Stecker bleibt verriegelt**

Damit beschreibt Coil 6 am verständlichsten die Option **„Stecker nach Ladeende verriegelt lassen“**.


---

# FC02 – Discrete Inputs

Die Adressen **1–8** sind lesbar. In den durchgeführten Tests spiegeln sie **immer exakt die entsprechenden FC01-Coils**.

Verifizierte Zuordnung:

| FC02 Discrete Input | entspricht |
|---:|---|
| 1 | Coil 1 |
| 2 | Coil 2 |
| 3 | Coil 3 |
| 4 | Coil 4 |
| 5 | Coil 5 |
| 6 | Coil 6 |
| 7 | Coil 7 |
| 8 | Coil 8 |

Beim ersten Scan war das Bitmuster bereits identisch:

```text
Adresse: 1 2 3 4 5 6 7 8
FC01:    1 1 0 0 0 0 1 0
FC02:    1 1 0 0 0 0 1 0
```

Danach wurden die Bits während verschiedener Coil-Änderungen weiter beobachtet. **Jeder Discrete Input folgte dabei seinem gleich adressierten Coil.**

Damit ist FC02 im getesteten Gerät ein **Read-Only-Spiegel des FC01-Bereichs 1–8** und liefert für die praktische Steuerung keine zusätzliche Information.

Für die IP-Symcon-Vorlage werden die FC02-Variablen deshalb weggelassen; gelesen und geschaltet wird über FC01.


---

# FC03 – Holding Register

## Registerübersicht

| Register | Zugriff | Datentyp / Aufbau | Bedeutung | Eigener Test / beobachtete Werte | Herkunft |
|---|---|---|---|---|---|
| **40001** | R | UINT16 / HEX | Kennung / Magic | 58332 = **0xE3DC** | Eigene Messung |
| **40002–40003** | RO getestet | UINT16 | Unbekannt | Grundzustand: 40002=1, 40003=0. FC06 abgewiesen; FC16 dort noch nicht separat geprüft. | Eigene Messung |
| **40004–40019** | R | 32 Byte ASCII, 2 Zeichen/Register | Hersteller | ergibt **HAGER**, Rest NUL-Padding | Eigene Messung |
| **40020–40035** | R | 32 Byte ASCII, 2 Zeichen/Register | Produktkennung | ergibt **XEVFR**, Rest NUL-Padding | Eigene Messung |
| **40036–40051** | R | 32 Byte ASCII, 2 Zeichen/Register | Gerätekennung | gerätespezifischer 32-Byte-String | Eigene Messung |
| **40052–40067** | R | 32 Byte ASCII, 2 Zeichen/Register | Versionsstring | **7.0.5.0/1.0.2.0**, Rest NUL-Padding | Eigene Messung |
| **40068** | R | UINT16 | I-Max-Drehkodierschalter | 0→32, 10A→10, 13A→13, 16A→16, 20A→20, 25A→25, 32A→32, A→65, B→66, C→67 | Eigene Messung + [Hager Anleitung](https://assets.hager.com/step-content/P/HA_39675796/Document/std.lang.all/6LE009000B_XEV1KXX2S_EVCS_WITTY-SOLAR_MANUAL_DE_WEB.pdf) |
| **40069** | R | UINT16 [A] | erkannter PP-Kabelnennstrom | kein Kabel→0, 13A→13, 20A→20, 32A→32, 63A→63 | Eigene Messung mit [Gossen Metrawatt PRO-TYP II](https://www.gossenmetrawatt.de/produkte/pro-typ-i-i/) |
| **40070** | R | 2 Byte ASCII | interner EVSE-/Ladezustandscode | A/N.C.→`F\0`, A+Kabel→`A1`, B→`A1`, C→`C2`, D→`D1`, E→`E\0` | Eigene Messung mit [PRO-TYP II](https://www.gossenmetrawatt.de/produkte/pro-typ-i-i/) |
| **40071** | R | UINT16 [%] | CP-PWM Duty Cycle | CP=C: PP13→21%, PP20→33%, PP32→53%, PP63→53%. Bei 40081=6A →10%, bei 32A →53%. | Eigene Messung |
| **40072** | R | UINT16 Rohwert | **Hypothese: Strom L1** | ohne Last 0; 1-phasiger Wasserkocher an L1 ca. 81–82; Übergang beim Ein/Aus u.a. ca. 44 | Eigene Messung |
| **40073–40074** | RO | UINT16 Rohwert | **Hypothese: Strom L2 / L3** | bisher 0; echte L2/L3-Last noch nicht getestet. FC06/FC16 abgewiesen. | Eigene Messung / Hypothese |
| **40075–40080** | RO | UINT16 | Unbekannt | bisher jeweils **65535 = 0xFFFF**; FC06/FC16 abgewiesen | Eigene Messung |
| **40081** | **RW** | UINT16 [A] | **Ladestrom-Sollwert / angebotener Maximalstrom** | 6A → 40071=10%; 32A → 40071=53%. FC06 und FC16 funktionieren. | Eigene Messung |
| **40082** | **RW** | UINT16 | Unbekannt | Grundwert 32. 6 und 32 werden angenommen. 40082=6 verändert bei 40081=32 den PWM-Wert nicht. | Eigene Messung |
| **40083** | **RW** | UINT16 / Enum | extern als Sonnenmodus beschrieben | 1 und 2 bleiben stehen; 3 springt auf 2 zurück. Im Standalone-Test keine direkte Änderung von 40070/40071. | Eigene Messung + [evcc Discussion #14122](https://github.com/evcc-io/evcc/discussions/14122) |
| **40084–40085** | RO | UINT16 | Unbekannt | FC06/FC16 abgewiesen | Eigene Messung |
| **40086–40089** | R | 8 Byte ASCII, 2 Zeichen/Register | **RFID-Karten-ID** | Karte `C08D62C4` wird exakt als `C0` + `8D` + `62` + `C4` gelesen | Eigene Messung |
| **40090–40101** | RO | UINT16 | Unbekannt | keine belastbare Zuordnung; FC06/FC16 abgewiesen | Eigene Messung |
| **40102** | **RO** | UINT16 | Unbekannt | Wert bleibt im Test konstant bei 3. Auch bei 1-phasigem Laden über Coil 3 = 1 keine Änderung. Schreibversuch auf 1 per FC06 → **ILLEGAL_DATA_ADDRESS**. | Eigene Messung |

---

## Detailtests der Holding Register

### 40068 – I-Max-Drehkodierschalter

| physische Stellung | Funktion / Beschriftung | 40068 |
|---:|---|---:|
| 0 | Auto über EMC | 32 |
| 1 | 10 A | 10 |
| 2 | 13 A | 13 |
| 3 | 16 A | 16 |
| 4 | 20 A | 20 |
| 5 | 25 A | 25 |
| 6 | 32 A | 32 |
| 7 | A | 65 |
| 8 | B | 66 |
| 9 | C | 67 |

65 / 66 / 67 sind zugleich die ASCII-Werte für **A / B / C**.

Die Bedeutung der Schalterstellungen ist zusätzlich in der [Hager Installationsanleitung](https://assets.hager.com/step-content/P/HA_39675796/Document/std.lang.all/6LE009000B_XEV1KXX2S_EVCS_WITTY-SOLAR_MANUAL_DE_WEB.pdf) beschrieben.

**Besonderheit:** Stellung 0 (Auto/EMC) und Stellung 32 A liefern beide den Registerwert 32. Der Registerwert allein kann diese beiden physischen Schalterstellungen daher nicht unterscheiden.

### 40069 – PP-Kabelerkennung

| PP-Simulation | 40069 |
|---|---:|
| kein Kabel | 0 |
| 13 A | 13 |
| 20 A | 20 |
| 32 A | 32 |
| 63 A | 63 |

Damit ist 40069 als erkannter Kabelnennstrom **verifiziert**.

### 40070 – interner Zustandsstring

| CP | PP | Dezimal | Hex | ASCII | Beobachtung |
|---|---:|---:|---:|---|---|
| A | N.C. | 17920 | 0x4600 | `F\0` | kein Kabel |
| A | 13/20/32/63 A | 16689 | 0x4131 | `A1` | Kabel erkannt |
| B | 13 A | 16689 | 0x4131 | `A1` | Verriegelung hörbar |
| C | 13 A | 17202 | 0x4332 | `C2` | Leistungsschütze hörbar |
| D | 13 A | 17457 | 0x4431 | `D1` | Belüftung gefordert |
| E | 13 A | 17664 | 0x4500 | `E\0` | Schütze fallen ab, Kabel entriegelt |

Wichtig: **CP=B ergibt weiterhin `A1`**. 40070 ist daher kein einfacher CP-Buchstabe, sondern ein interner Wallbox-Zustandscode.

### 40071 – CP-PWM Duty Cycle

| PP bei CP=C | 40071 |
|---:|---:|
| 13 A | 21 % |
| 20 A | 33 % |
| 32 A | 53 % |
| 63 A | 53 % |

Zusätzlich über 40081 bestätigt:

| 40081 | 40071 |
|---:|---:|
| 6 A | 10 % |
| 32 A | 53 % |

Damit ist 40071 als PWM-Duty-Cycle **verifiziert**.

### 40072–40074 – mögliche Phasenströme

40072 reagiert auf eine reale Last an L1:

- ohne Last: 0
- 2200-W-Wasserkocher an L1: ca. 81–82
- beim Ein-/Ausschalten Zwischenwerte, u.a. ca. 44

Daher ist **40072 = Strom L1** sehr wahrscheinlich.

Die Skalierung ist noch offen. **0,1 A pro Digit** ist plausibel, aber noch nicht ausreichend gegen eine Referenzmessung bestätigt.

40073 und 40074 sind als **Strom L2 / Strom L3** vorgemerkt. Der Prüfadapter stellt die Steckdose nur auf L1 bereit; eine echte Gegenprobe mit L2/L3 steht noch aus.

### 40081 – Ladestrom-Sollwert

**RW verifiziert über FC06 und FC16.**

Bei CP=C / PP=32 A:

| 40081 | resultierender 40071 |
|---:|---:|
| 6 A | 10 % |
| 32 A | 53 % |

Damit ist 40081 der direkte Ladestrom-Sollwert in Ampere.

### 40082 – unbekanntes RW-Register

- Grundwert: 32
- 6 und 32 lassen sich schreiben
- FC06 und FC16 funktionieren
- 40082=6 verändert bei 40081=32 den CP-PWM-Wert nicht

Eine Funktion als Sicherungs-/Installationsgrenze wäre denkbar, ist aber **nicht belegt**.

### 40083 – extern als Sonnenmodus beschrieben

Eigene Tests:

- 1 bleibt stehen
- 2 bleibt stehen
- 3 wird wieder auf 2 zurückgesetzt
- FC06 und FC16 funktionieren
- zwischen 1 und 2 keine unmittelbare Änderung von 40070/40071 im Standalone-Test

Externe Herkunft: [evcc Discussion #14122](https://github.com/evcc-io/evcc/discussions/14122)

Dort wurde per Wireshark ein direkter Schreibzugriff auf **Modbus-Adresse 82** der Wallbox beobachtet:

- 1 = Sonnenmodus aus / Mischbetrieb
- 2 = Sonnenmodus ein

Da die Modbus-PDU-Adresse 82 in unserer 40001-Darstellung Register **40083** entspricht, passt das zu unserem beschreibbaren Register.

Die Bedeutung 1/2 bleibt trotzdem als **Fremdquelle** gekennzeichnet, solange die Funktion ohne EMC nicht selbst ausgelöst und bestätigt wurde.

### 40086–40089 – RFID-Karten-ID

Testkarte: **C08D62C4**

| Register | Dezimal | Hex | ASCII |
|---:|---:|---:|---|
| 40086 | 17200 | 0x4330 | `C0` |
| 40087 | 14404 | 0x3844 | `8D` |
| 40088 | 13874 | 0x3632 | `62` |
| 40089 | 17204 | 0x4334 | `C4` |

Zusammengesetzt:

```text
C0 + 8D + 62 + C4 = C08D62C4
```

Damit ist der Aufbau als 8-Byte-ASCII-ID über vier Register **verifiziert**.

Noch offen ist, wie die Autorisierung bzw. Ladefreigabe über Modbus erfolgt.

### 40102 – unbekanntes RO-Register

Bisherige Tests:

- gelesener Wert: **3**
- FC06-Schreibversuch auf 1 → **ILLEGAL_DATA_ADDRESS**
- 3-phasiges Laden → Wert bleibt **3**
- 1-phasiges Laden über **Coil 3 = 1** → Wert bleibt ebenfalls **3**

Damit ist die frühere Hypothese **„aktive/verfügbare Phasenanzahl“ widerlegt**.

40102 ist weiterhin ein lesbares, aber nicht beschreibbares Register mit aktuell unbekannter Bedeutung.


---


## Geplanter Ablauf für Phasenumschaltung während einer Ladesitzung

Aus den bisherigen Tests ergibt sich ein möglicher Ablauf, um die Phasenumschaltung ohne Abziehen des Fahrzeugs zu erreichen.

Wichtig: Der vollständige Ablauf ist **noch nicht mit einem realen Fahrzeug verifiziert**. Sicher bestätigt sind bisher nur:
- Coil 3 schaltet die Phasenwahl beim nächsten Ladebeginn.
- Eine Änderung von Coil 3 während laufender Ladung wird nicht übernommen.
- 40081 steuert den angebotenen Ladestrom / CP-PWM.

### Ausgangszustand 3-phasig

Beispiel:

```text
CP = A
40081 Ladestrom-Sollwert = 32 A
Coil 3 Phasenwahl = 0   (3-phasig)

danach:
CP = B

danach:
CP = C
```

Das entspricht einem normalen Start mit 3-phasiger Phasenwahl.

### Vorgeschlagene Umschaltsequenz 3P → 1P

Die Idee ist, den angebotenen Ladestrom zunächst auf **0 A** zu setzen, damit das Fahrzeug die Leistungsaufnahme beendet und die Wallbox den Leistungspfad abschalten kann.

```text
40081 Ladestrom-Sollwert = 0 A
→ Fahrzeug soll theoretisch aufhören zu laden
→ erwarteter Zustand entspricht funktional CP = B
→ Leistungsschütze sollen abfallen

Coil 3 Phasenwahl = 1   (1-phasig)

40081 Ladestrom-Sollwert = 32 A
→ Fahrzeug soll erneut Ladefreigabe erhalten
→ erwarteter Zustand entspricht funktional CP = C
→ nur L1 soll zugeschaltet werden
```

### Erwartetes Prinzip

Damit wäre die Phasenumschaltung prinzipiell:

```text
Ladestrom auf 0 A
→ warten bis Leistungsschütze abgefallen sind
→ Coil 3 auf gewünschte Phasenart setzen
→ Ladestrom wieder > 0 A setzen
```

Für 1P → 3P entsprechend umgekehrt:

```text
40081 = 0 A
→ Schütze aus
→ Coil 3 = 0
→ 40081 wieder auf gewünschten Ladestrom
```

### Noch zu verifizieren

Bei einem realen Fahrzeug muss noch geprüft werden:

1. ob **40081 = 0 A** tatsächlich einen sauberen Ladestopp auslöst,
2. ob dadurch der Wallboxzustand zuverlässig auf einen Zustand mit abgefallenen Schützen zurückgeht,
3. ob danach Coil 3 übernommen wird,
4. ob das erneute Setzen von 40081 > 0 A die Ladung automatisch wieder startet,
5. ob dafür zusätzliche Wartezeiten oder eine weitere Freigabe erforderlich sind.

Bis diese Sequenz reproduzierbar bestätigt ist, bleibt sie als **Hypothese / geplanter Testablauf** dokumentiert.

---

## Schreibtests Holding Register

### Sicher als RW bestätigt

| Register | FC06 | FC16 | Bedeutung |
|---:|:---:|:---:|---|
| 40081 | ✅ | ✅ | Ladestrom-Sollwert |
| 40082 | ✅ | ✅ | unbekannt |
| 40083 | ✅ | ✅ | extern Sonnenmodus |

### Als nicht beschreibbar getestet

FC06 und FC16 wurden für folgende Bereiche getestet und abgewiesen:

- 40073–40080
- 40084–40085
- 40090–40101

Für 40002/40003 wurde FC06 abgewiesen; FC16 ist dort noch nicht separat dokumentiert.

40102 wurde per FC06 abgewiesen.

---

# FC04 – Input Register

Im vollständigen Scan von Adresse 0–1000 wurde **kein gültiges FC04 Input Register** gefunden.

Details zum Scan stehen in der [README](../README.md#durchgeführte-adressscans).

---

## Externe Quellen zur Registerzuordnung

### Hager-Dokumentation

[Hager Installationsanleitung witty solar XEV1K22T2S / XEV1K07T2S](https://assets.hager.com/step-content/P/HA_39675796/Document/std.lang.all/6LE009000B_XEV1KXX2S_EVCS_WITTY-SOLAR_MANUAL_DE_WEB.pdf)

Verwendet für:

- Bedeutung des I-Max-Drehkodierschalters
- Auto/EMC-Stellung
- Stromstufen 10/13/16/20/25/32 A

### evcc

[evcc Discussion #14122 – Support für E3/DC Multi Connect II](https://github.com/evcc-io/evcc/discussions/14122)

Verwendet ausschließlich für den Fremdfund:

- Modbus-Adresse 82
- 1 = Sonnenmodus aus / Mischbetrieb
- 2 = Sonnenmodus ein

### Photovoltaikforum

[Photovoltaikforum – Modbus Register E3DC Wallbox Multi Connect](https://www.photovoltaikforum.com/thread/198110-modbus-register-e3dc-wallbox-multi-connect/)

Dort wurde bereits ein direkter Register-Dump der Wallbox diskutiert. Die eigenen Messungen dieses Projekts gehen darüber hinaus und untersuchen den gültigen Holding-Register-Bereich bis 40102 sowie die Bitbereiche FC01/FC02.

---

## Offene Punkte

1. Phasenumschalt-Sequenz am realen Fahrzeug verifizieren: **40081=0 A → Schütze aus → Coil 3 ändern → 40081 wieder >0 A**
3. Bedeutung von **40082**
4. Bedeutung von **40075–40080**
5. L2/L3-Gegenprobe für **40073/40074**
6. exakte Skalierung von **40072–40074**
7. Modbus-Mechanismus für **RFID-Autorisierung / Ladefreigabe**
8. Leistungs- und Energiezählerregister
9. Bedeutung der noch offenen **Coils 2, 4 und 5**
10. Coil 0 separat nachprüfen
11. FC02 ist als vollständiger Read-Only-Spiegel von FC01 1–8 bestätigt; keine weitere Funktionszuordnung erforderlich
