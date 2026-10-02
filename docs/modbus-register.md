# Direkte Modbus-TCP-Register der E3/DC Multi Connect II

> Reverse-Engineering-Dokumentation für den direkten Zugriff auf die Wallbox **ohne E3/DC-Hauskraftwerk**.  
> Testgerät: **E3/DC Multi Connect II – XEV1K22T2E3DC**, 22 kW / 32 A, Baujahr 22.10.2024.

## Stand

**Messstand: 02.10.2026**

Bisher verwendet:

- Modbus TCP, Port **502**
- Lesen: **FC03 – Read Holding Registers**
- Schreiben: **FC06 – Write Single Register** und **FC16 – Write Multiple Registers**
- Registerdarstellung hier: **40001-basiert**
- IP-Symcon / Modbus-PDU sind 0-basiert:
  - 40001 → Adresse 0
  - 40081 → Adresse 80
  - 40083 → Adresse 82
  - 40102 → Adresse 101

**40000 wird in dieser Doku nicht als eigenes Register geführt.** Der Modbus-PDU-Offset 0 entspricht hier 40001.

Ein vollständiger **FC03-Adressscan mit Modbus Poll über die PDU-Adressen 0–1000** bestätigt den gültigen Holding-Register-Bereich exakt: **Adresse 0–101 antwortet mit `Response ok`, ab Adresse 102 bis einschließlich 1000 kommt durchgehend Modbus-Exception `02 Illegal Data Address`.** In der hier verwendeten 40001-Darstellung entspricht das **40001–40102 gültig** und **40103–41001 nicht vorhanden**.

---


## Vollständiger FC03-Adressscan

Am 02.10.2026 wurde mit **Modbus Poll** ein Address Scan durchgeführt:

- Slave ID: **1**
- Funktion: **03 Read Holding Registers (4x)**
- Start Address: **0**
- End Address: **1000**
- Modbus TCP: Port **502**

Ergebnis:

| PDU-Adresse | 40001-Darstellung | Ergebnis |
|---:|---:|---|
| 0–101 | 40001–40102 | **Response ok** |
| 102–1000 | 40103–41001 | **02 Illegal Data Address** |

Damit ist für die getestete Wallbox/Firmware der direkt per **FC03 lesbare Holding-Register-Bereich auf 40001–40102 begrenzt**. Es wurden im Bereich 40103–41001 keine weiteren Holding Register gefunden.

Wichtig: Das schließt **andere Modbus-Funktionsbereiche** wie Coils, Discrete Inputs oder Input Registers nicht aus.

---


## FC01 – Read Coils

Am 02.10.2026 wurde mit **Modbus Poll** ein vollständiger Address Scan für **FC01 Read Coils** über die PDU-Adressen **0–1000** durchgeführt.

Beim Scan waren die Adressen 1–8 lesbar; ab Adresse 9 kam durchgehend **02 Illegal Data Address**.

### Aktueller Stand der Coils

| Coil | Zugriff | Bedeutung | Eigener Test / Verhalten | Status |
|---:|---|---|---|---|
| **1** | RW | **Steckerverriegelung / Verriegelungsfreigabe** | Coil 1 von 1 auf 0 gesetzt → der Stecker wird nicht mehr verriegelt. Gleichzeitig geht Discrete Input 1 auf 0. | **verifiziert** |
| **2** | RW | Unbekannt | Coil 2 auf 0 gesetzt → Laden weiterhin möglich; beide Leistungsschütze ziehen an. Damit im getesteten Zustand weder Ladefreigabe noch 1P-Auswahl. | getestet, Bedeutung offen |
| **3** | RW | **Phasenwahl: 0 = 3-phasig, 1 = 1-phasig** | Coil 3 auf 1 gesetzt und Ladevorgang neu gestartet → nur ein Leistungsschütz zieht an und nur **L1** wird durchgeschaltet. Bei Coil 3 = 0 wurden im Vergleich beide Schütze angesteuert. | **verifiziert** |
| 4 | RW getestet | Unbekannt | noch keine Funktion zugeordnet | offen |
| 5 | RW getestet | Unbekannt | noch keine Funktion zugeordnet | offen |
| 6 | RW getestet | Unbekannt | noch keine Funktion zugeordnet | offen |
| 7 | **RO** | Unbekannt | Lesen möglich; Schreibversuch per FC05 → **02 Illegal Data Address** | Schreibzugriff abgewiesen |
| 8 | **RO** | Unbekannt | Lesen möglich; Schreibversuch per FC05 → **02 Illegal Data Address** | Schreibzugriff abgewiesen |

### Verhalten der Phasenwahl über Coil 3

Die Phasenwahl wird **nicht während eines laufenden Ladevorgangs übernommen**:

- Laden läuft 3-phasig → Coil 3 auf 1 setzen → laufender Zustand bleibt unverändert.
- Coil 3 muss vor dem nächsten Ladebeginn gesetzt werden.
- Beim anschließenden Start mit Coil 3 = 1 zieht nur der 1-phasige Leistungspfad an; L1 liegt an, L2/L3 bleiben aus.

Damit ist Coil 3 der bisher gefundene direkte Modbus-Befehl für die **1P/3P-Umschaltung**.

Für eine Regelung sollte daher vor dem Umschalten der Ladevorgang beendet, Coil 3 gesetzt und anschließend neu gestartet werden.

### Ursprünglicher FC01-Scan

```text
Adresse: 1 2 3 4 5 6 7 8
Wert:    1 1 0 0 0 0 1 0
```

Ab Coil 9 bis 1000: **02 Illegal Data Address**.

**Coil 0:** Im Modbus-Poll-Export erschien `Write error`; deshalb weiterhin separat zu behandeln.

---

## FC02 – Read Discrete Inputs

Am 02.10.2026 wurde mit **Modbus Poll** ein vollständiger Address Scan für **FC02 Read Discrete Inputs** über die PDU-Adressen **0–1000** durchgeführt.

Scan-Ergebnis:

| Discrete Input | Wert beim Scan | Ergebnis | Stand |
|---:|:---:|---|---|
| 0 | 0 | `Write error` im Modbus-Poll-Export | nochmals manuell prüfen; kein belastbarer FC02-Befund |
| 1 | **1** | Response ok | **Verriegelungsstatus/-freigabe; folgt Coil 1 im Test** |
| 2 | **1** | Response ok | vorhanden, Bedeutung unbekannt |
| 3 | 0 | Response ok | vorhanden, Bedeutung unbekannt |
| 4 | 0 | Response ok | vorhanden, Bedeutung unbekannt |
| 5 | 0 | Response ok | vorhanden, Bedeutung unbekannt |
| 6 | 0 | Response ok | vorhanden, Bedeutung unbekannt |
| 7 | **1** | Response ok | vorhanden, Bedeutung unbekannt |
| 8 | 0 | Response ok | vorhanden, Bedeutung unbekannt |
| 9–1000 | – | **02 Illegal Data Address** | keine weiteren Discrete Inputs gefunden |

Der FC02-Scan ist damit im aktuellen Zustand **identisch zum zuvor gemessenen FC01-Scan**:

```text
Adresse: 1 2 3 4 5 6 7 8
FC01:    1 1 0 0 0 0 1 0
FC02:    1 1 0 0 0 0 1 0
```

Mindestens für **Adresse 1** ist inzwischen ein Zusammenhang bestätigt: Wird Coil 1 auf 0 gesetzt und damit die Steckerverriegelung deaktiviert, geht auch **Discrete Input 1 auf 0**. Für die übrigen Discrete Inputs ist die Bedeutung weiterhin offen.

Gerade diese acht Bits sollten bei definierten Zustandswechseln weiter verglichen werden:

- CP A / B / C / D / E
- Kabel vorhanden / nicht vorhanden
- RFID-Karte erkannt
- Ladefreigabe
- Leistungsschütze ein / aus
- spätere 1P/3P-Umschaltung

**Hinweis zu Adresse 0:** Wie bereits beim FC01-Scan meldet der Modbus-Poll-Export für Adresse 0 `Write error`. Deshalb wird Adresse 0 separat behandelt und nicht als gültig oder ungültig bewertet.

---


## FC04 – Read Input Registers

Am 02.10.2026 wurde mit **Modbus Poll** ein vollständiger Address Scan für **FC04 Read Input Registers** über die PDU-Adressen **0–1000** durchgeführt.

Ergebnis:

| PDU-Adresse | Ergebnis |
|---:|---|
| 0–1000 | **02 Illegal Data Address** |

Damit wurden bei dieser Wallbox/Firmware im geprüften Bereich **keine Input Register (FC04)** gefunden.

Der gesamte Bereich 0–1000 wurde geprüft; es gab **keine einzige gültige FC04-Antwort**. Die bisher bekannten Mess- und Statuswerte liegen damit weiterhin im **Holding-Register-Bereich FC03** bzw. in den bereits gefundenen Bit-Bereichen FC01/FC02.

---

## Statuskennzeichnung

- **Verifiziert** – am eigenen Gerät reproduzierbar getestet.
- **Hypothese** – plausibel, aber Gegenprobe fehlt.
- **Fremdquelle** – Bedeutung stammt aus einer externen Quelle.
- **RO** – Schreiben wurde mit FC06 und/oder FC16 abgewiesen.
- **RW** – Schreiben wurde angenommen und zurückgelesen.

---

## ASCII-Codierung der Register

Mehrere Bereiche verwenden **zwei ASCII-Zeichen pro 16-Bit-Register**.

Beispiel:

- Registerwert dezimal: **17202**
- hexadezimal: **0x4332**
- High-Byte: **0x43** → ASCII **C**
- Low-Byte: **0x32** → ASCII **2**
- Ergebnis: **"C2"**

Die Byte-Reihenfolge ist bei den bisher beobachteten Textfeldern damit:

```text
Register 0x4142  ->  "AB"
         ^  ^
         |  +---- Low-Byte  = zweites Zeichen
         +------- High-Byte = erstes Zeichen
```

Ein Nullbyte beendet bzw. füllt den String auf:

```text
0x4600 -> "F\0"
0x5200 -> "R\0"
0x0000 -> zwei NUL-Bytes / Padding
```

Bei zusammenhängenden Textfeldern werden die Register einfach der Reihe nach zusammengesetzt.

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
| **40102** | R | UINT16 | **Hypothese: aktive/verfügbare Phasenanzahl** | Wert aktuell 3. Schreibversuch auf 1 per FC06 → **ILLEGAL_DATA_ADDRESS** | Eigene Messung / Hypothese |
| **40103–41001** | nicht vorhanden via FC03 | – | kein Holding-Register-Bereich | vollständiger Modbus-Poll-Scan: PDU 102–1000 liefert durchgehend **02 Illegal Data Address** | Eigener FC03-Adressscan mit Modbus Poll |

---

## Detailtests

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

**Besonderheit:** Stellung 0 (Auto/EMC) und Stellung 32 A liefern beide den Registerwert 32.

---

### 40069 – PP-Kabelerkennung

Getestet mit dem [Gossen Metrawatt PRO-TYP II](https://www.gossenmetrawatt.de/produkte/pro-typ-i-i/).

| PP-Simulation | 40069 |
|---|---:|
| kein Kabel | 0 |
| 13 A | 13 |
| 20 A | 20 |
| 32 A | 32 |
| 63 A | 63 |

Damit ist 40069 als erkannter Kabelnennstrom **verifiziert**.

---

### 40070 – interner Zustandsstring

Die CP-Zustände wurden mit dem [Gossen Metrawatt PRO-TYP II](https://www.gossenmetrawatt.de/produkte/pro-typ-i-i/) simuliert.

| CP | PP | Dezimal | Hex | ASCII | Beobachtung |
|---|---:|---:|---:|---|---|
| A | N.C. | 17920 | 0x4600 | `F\0` | kein Kabel |
| A | 13/20/32/63 A | 16689 | 0x4131 | `A1` | Kabel erkannt |
| B | 13 A | 16689 | 0x4131 | `A1` | Verriegelung hörbar |
| C | 13 A | 17202 | 0x4332 | `C2` | Leistungsschütze hörbar |
| D | 13 A | 17457 | 0x4431 | `D1` | Belüftung gefordert |
| E | 13 A | 17664 | 0x4500 | `E\0` | Schütze fallen ab, Kabel entriegelt |

Wichtig: **CP=B ergibt weiterhin `A1`**. 40070 ist daher kein einfacher CP-Buchstabe, sondern ein interner Wallbox-Zustandscode.

---

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

---

### 40072–40074 – mögliche Phasenströme

40072 reagiert auf eine reale Last an L1:

- ohne Last: 0
- 2200-W-Wasserkocher an L1: ca. 81–82
- beim Ein-/Ausschalten Zwischenwerte, u.a. ca. 44

Daher ist **40072 = Strom L1** sehr wahrscheinlich.

Die Skalierung ist noch offen. **0,1 A pro Digit** ist plausibel, aber noch nicht ausreichend gegen eine Referenzmessung bestätigt.

40073 und 40074 sind als **Strom L2 / Strom L3** vorgemerkt. Der Prüfadapter stellt die Steckdose jedoch nur auf L1 bereit; eine echte Gegenprobe mit L2/L3 steht noch aus.

---

### 40081 – Ladestrom-Sollwert

**RW verifiziert über FC06 und FC16.**

Bei CP=C / PP=32 A:

| 40081 | resultierender 40071 |
|---:|---:|
| 6 A | 10 % |
| 32 A | 53 % |

Damit ist 40081 der direkte Ladestrom-Sollwert in Ampere.

---

### 40082 – unbekanntes RW-Register

- Grundwert: 32
- 6 und 32 lassen sich schreiben
- FC06 und FC16 funktionieren
- 40082=6 verändert bei 40081=32 den CP-PWM-Wert nicht

Eine Funktion als Sicherungs-/Installationsgrenze wäre denkbar, ist aber **nicht belegt**.

---

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

Da die Modbus-PDU-Adresse 82 in unserer 40001-Darstellung dem Register **40083** entspricht, passt das exakt zu unserem beschreibbaren Register.

Die **Bedeutung 1/2** bleibt trotzdem als Fremdquelle gekennzeichnet, solange wir die Funktion ohne EMC nicht selbst auslösen können.

---

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

---

### 40102 – Phasenanzahl, Hypothese

- gelesener Wert: 3
- FC06-Schreibversuch auf 1 → **ILLEGAL_DATA_ADDRESS**
- noch keine echte 1P/3P-Umschaltung erzeugt

Daher aktuell nur:

> **Hypothese: aktive oder verfügbare Phasenanzahl**

Bestätigt wäre das erst, wenn bei einer realen Phasenumschaltung der Wert zwischen 1 und 3 wechselt.

---

## Schreibtests

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

## Testaufbau / Quellen

### Eigene Messungen

Alle als **verifiziert** markierten Register wurden direkt an der Wallbox XEV1K22T2E3DC gemessen.

### Wallbox / Hager-Dokumentation

[Hager Installationsanleitung witty solar XEV1K22T2S / XEV1K07T2S](https://assets.hager.com/step-content/P/HA_39675796/Document/std.lang.all/6LE009000B_XEV1KXX2S_EVCS_WITTY-SOLAR_MANUAL_DE_WEB.pdf)

Verwendet für:

- Bedeutung des I-Max-Drehkodierschalters
- Auto/EMC-Stellung
- Stromstufen 10/13/16/20/25/32 A

### Prüfadapter

[Gossen Metrawatt PRO-TYP II](https://www.gossenmetrawatt.de/produkte/pro-typ-i-i/)

Verwendet für:

- CP-Zustände A–E
- PP-Kabelsimulation 13/20/32/63 A
- reproduzierbare Fahrzeugsimulation

### Externe Modbus-Erkenntnis

[evcc Discussion #14122 – Support für E3/DC Multi Connect II](https://github.com/evcc-io/evcc/discussions/14122)

Verwendet ausschließlich für:

- Fremdfund Modbus-Adresse 82
- 1 = Sonnenmodus aus / Mischbetrieb
- 2 = Sonnenmodus ein

### Früher Community-Dump

[Photovoltaikforum – Modbus Register E3DC Wallbox Multi Connect](https://www.photovoltaikforum.com/thread/198110-modbus-register-e3dc-wallbox-multi-connect/)

Dort wurde bereits ein direkter Register-Dump der Wallbox diskutiert. Unsere eigenen Messungen gehen darüber hinaus; insbesondere wurden von uns Register bis 40102 untersucht.

---

## Offene Punkte

1. **1P/3P-Umschaltung gefunden:** Coil 3; noch Zusammenhang zu 40102 und Discrete Inputs prüfen
2. Bestätigung von **40102** als Phasenanzahl: nach Neustart mit Coil 3 = 1 prüfen, ob 40102 von 3 auf 1 wechselt
3. Bedeutung von **40082**
4. Bedeutung von **40075–40080**
5. L2/L3-Gegenprobe für **40073/40074**
6. exakte Skalierung von **40072–40074**
7. Modbus-Mechanismus für **RFID-Autorisierung / Ladefreigabe**
8. Leistungs- und Energiezählerregister
9. Bedeutung der noch offenen **Coils 2 und 4–6** ermitteln; **Coils 7/8 sind lesbar, aber per FC05 nicht schreibbar**; Coil 0 separat nachprüfen
10. Bedeutung der über FC02 gefundenen **Discrete Inputs 1–8** ermitteln; FC02 entspricht aktuell exakt FC01
11. **FC04 Input Registers:** Bereich 0–1000 vollständig negativ gescannt (durchgehend 02 Illegal Data Address)
12. Bedeutung der über FC01/FC02 gefundenen Bits weiter untersuchen
