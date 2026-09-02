---
paths:
  - 'app/Imports/**'
---

# Imports

## Maatwebsite Excel mengabaikan headingRowFormatter()
Di maatwebsite/excel 4.0, method headingRowFormatter() pada import class TIDAK dipakai. Heading dirubah ke snake_case oleh config excel.imports.heading_row.formatter (default 'slug'). Jadi rules() dan collection() harus memakai key snake_case (mis. $row['nama_standar'], bukan $row['Nama Standar']).
