# E3/DC Multi Connect II – Modbus-TCP-Register und Funktionen

Diese Seite dokumentiert die direkt an der **E3/DC Multi Connect II Wallbox** gefundenen und getesteten **Modbus-TCP-Register, Coils und Funktionen**.

Sie enthält ausschließlich die **technische Zuordnung der gefundenen Modbus-Adressen**, deren Bedeutung sowie die dazu durchgeführten Funktionstests.

Testaufbau, Hardware und Kommunikationsparameter sind in der [README](../README.md) beschrieben.

**Firmwarestand des getesteten Geräts: 7.0.5.0/1.0.2.0**

## Bisher gescannte Modbus-Bereiche

| Funktion | Bisher gescannter Bereich | Gefundene gültige Bereiche |
|---|---|---|
| **FC01 – Read Coils** | 0–9999 | **0–8** |
| **FC02 – Read Discrete Inputs** | 0–1000 | **1–8** |
| **FC03 – Read Holding Registers** | 0–60000 | **40001–40102**, **50000–50032**, **55000–55126** |
| **FC04 – Read Input Registers** | 0–1000 | **keine gültigen Adressen gefunden** |

Nachweis der Scans:
- [Modbus TCP Scans](<./Modbus TCP Scans/>)

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
| 9999 | 50000 |
| 10031 | 50032 |
| 14999 | 55000 |
| 15125 | 55126 |

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

Die Adressen **0–8** sind lesbar.

## Übersicht

| Coil | Zugriff | Bedeutung | Test / Verhalten | Status |
|---:|---|---|---|---|
| **0** | **RO** | Unbekannt | Lesen über FC01 möglich; FC05-Schreibversuch auf Adresse 0 → **02 Illegal Data Address**. | Schreibzugriff abgewiesen; Bedeutung offen |\n| **1** | **RW** | **Steckerverriegelung / Verriegelungsfreigabe** | 1 → 0 gesetzt: Stecker wird nicht mehr verriegelt. Gleichzeitig geht Discrete Input 1 auf 0. | **Verifiziert** |
| **2** | **RW** | Unbekannt | Auf 0 gesetzt: Laden weiterhin möglich; beide Leistungsschütze ziehen an. Damit im getesteten Zustand weder Ladefreigabe noch 1P-Auswahl. | Bedeutung offen |
| **3** | **RW** | **Phasenwahl** | 0 = 3-phasig, 1 = 1-phasig. Wirkung wird beim nächsten Ladebeginn übernommen. | **Verifiziert** |
| **4** | **RW-Test** | Unbekannt | Lesen und FC05-Schreiben möglich. Beobachtung: Coil 4 fällt selbstständig etwa alle **60 s auf FALSE** zurück. Funktion noch nicht zugeordnet. | Bedeutung offen; periodisches Rücksetzen beobachtet |
| **5** | **RW** | **Boost-Anforderung über Frontsensor** | Frontsensor 1–4 s betätigt → Coil 5 wird TRUE. Erneutes Betätigen setzt ihn nicht zurück. Nach Modbus-Schreiben auf FALSE kann der Sensor ihn erneut auf TRUE setzen. | **Verifiziertes Verhalten; Rücksetzlogik offen** |
| **6** | **RW** | **Stecker nach Ladeende verriegelt lassen** | Coil 6 = 0 → beim Wechsel von CP B auf CP A wird der Stecker entriegelt. Coil 6 = 1 → beim gleichen Wechsel bleibt der Stecker verriegelt. | **Verifiziert** |
| **7** | **RO** | Unbekannt | Lesen möglich; FC05-Schreibversuch → **02 Illegal Data Address**. Bei CP=E bleibt Coil 7 **TRUE**, ohne Zustandsänderung. | Schreibzugriff abgewiesen; Bedeutung offen |
| **8** | **RO** | **blockierender Hardware-/Systemfehlerstatus** | Normale rote Blinkfehler (CP D/E): **FALSE**. Front-Flachbandkabel abgezogen / Fehlercode 128: **TRUE**. Simulierter Schützklebefehler / Fehlercode 130: **TRUE** und dort bis Steuersicherung AUS/EIN verriegelt. FC05 → **02 Illegal Data Address**. | **Verifiziert für Fehler 128 und 130; genaue Fehlergruppe offen** |

## Coil 0 – Bedeutung offen

**Adresse 0 ist gültig**. Im aufgenommenen Zustand war Coil 0 **TRUE**. Ein Schreibversuch mit **FC05 auf Adresse 0** wird mit **02 Illegal Data Address** abgewiesen. Coil 0 ist damit im getesteten Gerät **nur lesbar**; die Funktion bleibt unbekannt.

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


## Coil 4 – periodisches Rücksetzen

Coil 4 ist les- und per FC05 beschreibbar, die Funktion ist weiterhin unbekannt.

Neue Beobachtung:

- nach dem Setzen bzw. während der Beobachtung fällt Coil 4 regelmäßig ungefähr alle **60 Sekunden auf FALSE**
- dieses Verhalten tritt ohne bekannte direkte Benutzeraktion auf

Das spricht eher für einen **Trigger-/Anforderungswert oder einen zyklisch zurückgesetzten internen Zustand** als für einen dauerhaft gespeicherten Modus. Eine Funktionszuordnung ist daraus noch nicht möglich.

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

## Coil 7 – Verhalten bei CP E

Beim Wechsel auf **CP-Zustand E** wurde beobachtet:

- **40085 = 2**
- Coil 7 **bleibt TRUE**
- Coil 8 **bleibt FALSE**

Da Coil 7 bereits zuvor TRUE war und beim Übergang nach E **keine Zustandsänderung** zeigt, lässt sich Coil 7 daraus nicht dem CP-Zustand E oder dem Fehlerwert 2 zuordnen. Die Bedeutung bleibt offen.

## Coil 8 – Fehlerstatus / Schützklebeüberwachung

Bei einer Simulation eines klebenden 3-poligen Ausgangsschützes wurde Coil 8 **TRUE**.

Testzustand:

- Mode-3-Laden mit Last
- Eingang und Ausgang des 3-poligen Ausgangsschützes überbrückt
- Ladeende eingeleitet
- Schütz wird angesteuert auszuschalten, am Ausgang bleibt durch die Brücke jedoch Spannung bestehen

Gleichzeitig wurden beobachtet:

- Front-LED **rot**
- **40085 = 130**
- Zustandscode **40070 = `F\0`**
- **Coil 8 = TRUE**

Damit ist Coil 8 als **verriegelter Fehlerstatus** stark gestützt. Beim simulierten Schützklebefehler bleibt Coil 8 auch nach Entfernen der Fehlerursache und normalen Ladezustandswechseln TRUE.

Ein Reset war erst durch **Steuersicherung AUS / EIN** möglich. Danach wechselte Coil 8 auf **FALSE**.

Zusätzlich wurde bei einem **nicht gesteckten Flachbandkabel zur Front** beobachtet:

- **40085 = 128**
- **Coil 8 = TRUE**
- das Flachbandkabel verbindet die Front mit LED, Reader und Näherungssensor

Damit reagiert Coil 8 nicht nur auf den Schützklebefehler, sondern auch auf mindestens einen weiteren Hardware-/Systemfehler. Eine allgemeine Zuordnung zu **blockierenden Hardwarefehlern** ist damit deutlich besser gestützt.

Bei den reproduzierten **roten Blinkfehlern** bleibt Coil 8 dagegen **FALSE**:

- CP=E → 40085=2, Coil 8=FALSE
- CP=D und anschließend Kabel anstecken → 40085=4, Coil 8=FALSE

Erst der simulierte Schützklebefehler mit **rotem Dauerlicht** setzt Coil 8 auf TRUE und verriegelt den Zustand bis zum Spannungsreset.

Damit spricht Coil 8 sehr wahrscheinlich für einen **blockierenden / verriegelten Fehler**, der nicht durch normales Ab- und Anstecken des Ladekabels quittiert wird. Ob Coil 8 ausschließlich die Schützklebeüberwachung oder mehrere der im Handbuch genannten Dauerlicht-/Hardwarefehler abbildet, ist noch offen.

FC05-Schreiben auf Coil 8 wird mit **02 Illegal Data Address** abgewiesen; Coil 8 ist damit **RO**.

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

Das Bitmuster war im gleichen Zustand identisch:

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
| **40068** | R | UINT16 / Enum | I-Max-Drehkodierschalter | 10/13/16/20/25/32 werden als Stromstufe in A geliefert; A→65, B→66, C→67. Drehen unter Spannung auf A oder C löste Fehlercode **129** aus. | Eigene Messung + [Hager Anleitung](https://assets.hager.com/step-content/P/HA_39675796/Document/std.lang.all/6LE009000B_XEV1KXX2S_EVCS_WITTY-SOLAR_MANUAL_DE_WEB.pdf) |
| **40069** | R | UINT16 [A] | erkannter PP-Kabelnennstrom | kein Kabel→0, 13A→13, 20A→20, 32A→32, 63A→63 | Eigene Messung mit [Gossen Metrawatt PRO-TYP II](https://www.gossenmetrawatt.de/produkte/pro-typ-i-i/) |
| **40070** | R | 2 Byte ASCII | interner EVSE-/Ladezustandscode | A/N.C.→`F\0`, A+Kabel→`A1`, B→`A1`, C→`C2`, D→`D1`, E→`E\0` | Eigene Messung mit [PRO-TYP II](https://www.gossenmetrawatt.de/produkte/pro-typ-i-i/) |
| **40071** | R | UINT16 [%] | CP-PWM Duty Cycle | CP=C: PP13→21%, PP20→33%, PP32→53%, PP63→53%. Bei 40081=6A →10%, bei 32A →53%. | Eigene Messung |
| **40072** | RO | UINT16, **0,1 A/Digit** | **Strom L1** | Strommesszange BEHA AMPROBE AMP-310-EUR: **8,24 A**; gleichzeitig Registerwert **82**. Skalierung damit bestätigt. | Eigene Messung, verifiziert |
| **40073** | RO | UINT16, **0,1 A/Digit** | **Strom L2** | Mit Strommesszange auf L2 gegengeprüft; FC06/FC16 abgewiesen. | Eigene Messung, verifiziert |
| **40074** | RO | UINT16, **0,1 A/Digit** | **Strom L3** | Mit Strommesszange auf L3 gegengeprüft; FC06/FC16 abgewiesen. | Eigene Messung, verifiziert |
| **40075–40080** | RO | UINT16 | Unbekannt | bisher jeweils **65535 = 0xFFFF**; FC06/FC16 abgewiesen | Eigene Messung |
| **40081** | **RW** | UINT16 [A] | **Ladestrom-Sollwert / angebotener Maximalstrom** | 6A → 40071=10%; 32A → 40071=53%. FC06 und FC16 funktionieren. | Eigene Messung |
| **40082** | **RW** | UINT16 | Unbekannt | Grundwert 32. 6 und 32 werden angenommen. 40082=6 verändert bei 40081=32 den PWM-Wert nicht. | Eigene Messung |
| **40083** | **RW** | UINT16 / Enum | extern als Sonnenmodus beschrieben | 1 und 2 bleiben stehen; 3 springt auf 2 zurück. Im Standalone-Test keine direkte Änderung von 40070/40071. | Eigene Messung + [evcc Discussion #14122](https://github.com/evcc-io/evcc/discussions/14122) |
| **40084** | RO | UINT16 | Unbekannt | FC06/FC16 abgewiesen | Eigene Messung |
| **40085** | RO | UINT16 | **LED-/Fehlercode** | Normal **0**; CP=E → **2**; CP=D + Kabel → **4**; Codierschalter unter Spannung auf A/C gedreht → **129** verriegelnd; Schützklebefehler → **130**. | Eigene Messung + Handbuch |
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

#### Verhalten der Stellungen A / B / C

Zusätzliche Funktionstests ergaben:

- wird der Codierschalter bei bereits eingeschalteter Wallbox auf **A** oder **C** gedreht, erscheint **Fehlercode 129**
- der Fehler ist **verriegelnd / blockierend**
- wird die Wallbox bereits mit Stellung **A**, **B** oder **C** gestartet, leuchtet die Front-LED rot, es wird jedoch **kein Fehlercode in 40085** gesetzt und keine Blockierung festgestellt
- Start mit **A**: rote LED, kein Fehlercode, keine weitere feststellbare Reaktion
- Start mit **B**: rote LED; der **3-polige Schütz zieht für etwa 30 Sekunden an**, kein Fehlercode
- Start mit **C**: rote LED, kein Fehlercode, keine weitere feststellbare Reaktion
- wird nach einem Start mit A/B/C der Codierschalter in eine andere Position gedreht, wurde ebenfalls **kein Fehlercode** beobachtet

Damit unterscheidet die Wallbox offenbar zwischen einer beim Boot vorhandenen A/B/C-Stellung und einer Änderung des Codierschalters im laufenden Betrieb. Die genaue interne Bedeutung der drei Service-/Sonderstellungen bleibt offen.

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

Zusätzlich wurde beim simulierten Schützklebefehler nach Ladeende ebenfalls **`F\0`** gelesen, während die Front-LED rot leuchtete, 40085=130 und Coil 8=TRUE waren. `F\0` ist damit **kein eindeutiger Fehlercode** und kann allein den Schützklebefehler nicht kennzeichnen.

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

### 40072–40074 – Phasenströme

Die drei Register wurden mit einer **BEHA AMPROBE AMP-310-EUR** Strommesszange gegen reale Leiterströme geprüft.

Verifizierte Zuordnung:

| Register | Bedeutung | Skalierung |
|---:|---|---:|
| **40072** | Strom L1 | **0,1 A/Digit** |
| **40073** | Strom L2 | **0,1 A/Digit** |
| **40074** | Strom L3 | **0,1 A/Digit** |

Referenzmessung L1:

- Strommesszange: **8,24 A**
- Register 40072: **82**
- aus Registerwert berechnet: **8,2 A**

Die Abweichung von 0,04 A liegt im Rahmen der Mess- und Auflösungsgrenzen. Damit ist die Skalierung **0,1 A pro Digit** für L1 bestätigt.

L2 und L3 wurden ebenfalls mit der Strommesszange gegengeprüft und den Registern 40073 bzw. 40074 zugeordnet.

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

### 40085 – LED-/Fehlercode


### 40085 = 128 – Frontmodul / Flachbandkabel

Neuer Funktionstest:

- Flachbandkabel zur Front nicht gesteckt
- betroffenes Frontmodul enthält LED, Reader und Näherungssensor
- **40085 = 128**

Damit ist Fehlercode **128** für diesen Zustand reproduzierbar belegt. Gleichzeitig wird **Coil 8 = TRUE**. Welche einzelne Frontkomponente intern überwacht wird, ist damit noch nicht getrennt bestimmt; sicher ist der Zusammenhang mit dem fehlenden Front-Flachbandkabel.

### 40085 = 129 – Codierschalter während des Betriebs verstellt

Fehlercode **129** wurde reproduzierbar ausgelöst, wenn der **I-Max-Drehcodierschalter bei eingeschalteter Wallbox auf Stellung A oder C gedreht** wurde.

Beobachtung:

- **40085 = 129**
- Fehlerzustand **verriegelnd / blockierend**
- Auslösung bei Änderung auf **A** oder **C** während des laufenden Betriebs

Wichtig ist die Abgrenzung zum Startzustand: Wird die Wallbox bereits mit Stellung **A**, **B** oder **C** eingeschaltet, leuchtet die Front-LED zwar rot, **40085 bleibt jedoch ohne Fehlercode**.

Die bisherigen Tests zeigen einen deutlichen Zusammenhang zwischen Register 40085 und der roten LED-Fehleranzeige.

| Code | Bedeutung | Nachweis |
|---:|---|---|
| **0** | Kein Fehler | **selbst beobachtet** |
| **1** | Ladekabel defekt oder nicht unterstützt | **aus Betriebsanleitung abgeleitet** |
| **2** | Fahrzeugerkennungsfunktion funktioniert nicht | **Code selbst getestet; Bedeutung aus Betriebsanleitung** |
| **3** | Fahrzeug hält die vorgegebene Leistungsbeschränkung nicht ein | **aus Betriebsanleitung abgeleitet** |
| **4** | Fahrzeug/Wallbox nicht kompatibel; Fahrzeug erfordert Belüftung | **selbst getestet und Betriebsanleitung bestätigt** |
| **5** | Lastabwurf erfolgt zu häufig, da Hausanschlussleistung nicht ausreicht | **aus Betriebsanleitung abgeleitet** |
| **6** | Keine korrekte Freigabe vom Fahrzeug zum Ladebeginn | **aus Betriebsanleitung abgeleitet** |
| **8** | Gleichstromfehler über 6 mA in der Fahrzeugversorgung | **aus Betriebsanleitung abgeleitet** |
| **128** | Frontmodul nicht verbunden: Flachbandkabel zur Front mit LED, Reader und Näherungssensor nicht gesteckt | **selbst getestet** |
| **129** | Verriegelnder Fehler beim Verstellen des I-Max-Codierschalters im laufenden Betrieb auf Stellung A oder C | **selbst getestet** |
| **130** | Blockierender Fehler mit rotem Dauerlicht; im Test durch simulierte Schützklebeüberwachung ausgelöst | **selbst getestet** |

Die Betriebsanleitung ordnet den normalen roten Blinkfehlern die Blinkimpulse **1, 2, 3, 4, 5, 6 und 8** zu. Die eigenen Tests bestätigen bisher, dass **40085 denselben Zahlenwert liefert**:

- CP=E → 40085 = **2**
- CP=D und Kabel anstecken → 40085 = **4**

Der simulierte Schützklebefehler unterscheidet sich davon:

- 40085 = **130**
- Front-LED = **rotes Dauerlicht**
- Coil 8 = TRUE
- Fehler wird nicht durch Kabel aus-/einstecken quittiert
- Reset erst durch Steuersicherung AUS/EIN

Die Betriebsanleitung nennt für rotes Dauerlicht als mögliche Ursachen einen nicht arbeitenden 40-A-Schütz oder einen defekten/nicht angeschlossenen DC-Sensor. **130 ist bisher nur für den selbst simulierten Schützfehler bestätigt.**

#### Darstellung in IP-Symcon

Die Modbus-Vorlage enthält für 40085 das eigene Integer-Profil `E3DC.Wallbox.Fehlercode`.

Bekannte Fehlercodes werden dadurch direkt als Klartext dargestellt. Nicht bekannte Werte bleiben über die Assoziation **„Unbekannter Fehlercode %d“** mit ihrem tatsächlichen Zahlenwert sichtbar.

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

## FC03 – Holding Register 50000–50032

Der gültige Holding-Registerbereich **50000–50032** enthält unter anderem Netzwerkdaten.

### Auffällige Netzwerkdaten

Mehrere Registerwerte lassen sich sehr plausibel als Netzwerkparameter dekodieren:

| Register | Rohwert(e) | dekodiert | Einordnung |
|---:|---|---|---|
| **50010–50012** | 0x0C86 0x2970 0x507A | **0C:86:29:70:50:7A** | **MAC-Adresse – durch FRITZ!Box 7690 bestätigt** |
| **50013–50014** | 0xC0A8 0xB2F3 | **192.168.178.243** | **IPv4-Adresse – durch FRITZ!Box 7690 bestätigt** |
| **50015–50016** | 0xC0A8 0xB201 | **192.168.178.1** | **Gateway – durch FRITZ!Box 7690 bestätigt** |
| **50017–50018** | 0xFFFF 0xFF00 | **255.255.255.0** | **Subnetzmaske – durch FRITZ!Box 7690 bestätigt** |
| **50019–50020** | 0xC0A8 0xB201 | **192.168.178.1** | Vermutung: DNS / weiterer Netzparameter |
| **50023–50024** | 0xC0A8 0x00FE | **192.168.0.254** | Vermutung: weiterer / möglicher Fallback-Netzparameter |
| **50027–50028** | 0xFFFF 0xFF00 | **255.255.255.0** | Vermutung: zweite Subnetzmaske / Fallback-Konfiguration |

Die Struktur spricht stark für einen **Netzwerk-Konfigurationsblock**. **MAC-Adresse, IPv4-Adresse, Gateway und Subnetzmaske wurden durch Abgleich mit der FRITZ!Box 7690 bestätigt.** Die übrigen Netzwerkadressen sind weiterhin **Vermutungen** und noch nicht durch gezielte Änderung der Netzwerkeinstellungen verifiziert.

### Aufbau der Netzwerkwerte

Die Netzwerkwerte liegen direkt in den 16-Bit-Holding-Registern. Pro Register werden **zwei Oktette** gespeichert:

```text
High-Byte = erstes Oktett
Low-Byte  = zweites Oktett
```

Beispiel IPv4-Adresse:

```text
50013 = 0xC0A8 -> 192.168
50014 = 0xB2F3 -> 178.243

Ergebnis: 192.168.178.243
```

Eine IPv4-Adresse bzw. Subnetzmaske belegt damit **zwei Register**, eine MAC-Adresse **drei Register**.

### Vollständiger Rohbereich

- 50000–50009 = 0
- 50010–50012 = 0x0C86 / 0x2970 / 0x507A
- 50013–50014 = 0xC0A8 / 0xB2F3
- 50015–50016 = 0xC0A8 / 0xB201
- 50017–50018 = 0xFFFF / 0xFF00
- 50019–50020 = 0xC0A8 / 0xB201
- 50021–50022 = 0
- 50023–50024 = 0xC0A8 / 0x00FE
- 50025–50026 = 0
- 50027–50028 = 0xFFFF / 0xFF00
- 50029–50032 = 0

---

## FC03 – Holding Register 55000–55126

### Beobachtete Werte

Im aufgenommenen Grundzustand gilt:

- **55001 = 8**
- **55000 sowie 55002–55126 = 0**

Eine Funktionszuordnung ist daraus noch nicht möglich. Die Schreibbarkeit wurde nicht getestet. In der IP-Symcon-Vorlage wird der gesamte Block daher vorsorglich **nur lesend** angelegt und mit englischen Unknown-Idents geführt.

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

Dort wurde bereits ein direkter Register-Dump der Wallbox diskutiert. Die eigenen Messungen dieses Projekts gehen darüber hinaus und untersuchen die gültigen Holding-Register-Bereiche **40001–40102**, **50000–50032** und **55000–55126** sowie die Bitbereiche FC01/FC02.

---

## Offene Punkte

1. Phasenumschalt-Sequenz am realen Fahrzeug verifizieren: **40081=0 A → Schütze aus → Coil 3 ändern → 40081 wieder >0 A**
3. Bedeutung von **40082**
4. Bedeutung von **40075–40080**
5. Modbus-Mechanismus für **RFID-Autorisierung / Ladefreigabe**
6. Leistungs- und Energiezählerregister
7. Bedeutung der noch offenen **Coils 2 und 4**
8. Zusammenspiel **Coil 5 / 40083** und Rücksetzlogik des Frontsensor-Bits klären
9. Bedeutung und Schreibbarkeit von **Coil 0** prüfen
10. **Coil 8** weiter eingrenzen: aktuell TRUE bei Fehlercode 128 (Front-Flachbandkabel/Frontmodul nicht verbunden) und 130 (Schützklebefehler), FALSE bei den getesteten Blinkfehlern 2/4
11. Weitere **40085-Fehlercodes** prüfen: aktuell 0=normal, 2=CP E, 4=CP D, 128=Front-Flachbandkabel/Frontmodul nicht verbunden, 129=Codierschalter unter Spannung auf A/C verstellt, 130=Schützklebefehler.
12. Bedeutung des Blocks **55000–55126** klären; aktuell nur 55001=8, Rest 0
13. FC02 ist als vollständiger Read-Only-Spiegel von FC01 1–8 bestätigt; keine weitere Funktionszuordnung erforderlich
