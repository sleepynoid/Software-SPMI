<template>
    <div class="c1">
        <input type="file" @change="handleFileUpload" accept=".xlsx" />
        <table>
            <thead>
                <tr v-for="(header, index) in headers" :key="index">
                    <th :colspan="header.colspan" :rowspan="header.rowspan" class="text-center">
                        {{ header.value }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="row in groupedRows">
                    <td v-for="cell in row" :colspan="cell.colspan" :rowspan="cell.rowspan">
                        {{ cell.value }}
                    </td>
                </tr>
            </tbody>
        </table>
        <button @click="exportToExcel">Ekspor ke Excel</button>
    </div>
</template>

<script>
import * as XLSX from "xlsx";

export default {
    data() {
        return {
            headers: [],
            rows: [],
            groupedRows: [],
            hiddenHeaders: [],
        };
    },
    created() {
        console.log("Excel component has been created");
    },
    methods: {
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: "array" });
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const jsonSheet = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                    console.log("JSON Sheet:", jsonSheet); // Debug output

                    const merges = worksheet['!merges'] || [];
                    console.log("Merges:", merges); // Debug output

                    this.updateData(jsonSheet, merges);
                };
                reader.onerror = (error) => {
                    console.error("Error reading file:", error);
                };
                reader.readAsArrayBuffer(file);
            }
        },
        updateData(jsonSheet, merges) {
            this.headers = jsonSheet[0].map((header) => ({ value: header, rowspan: 1, colspan: 1 }));
            this.rows = jsonSheet.slice(1).map((row) => row.map((cell) => ({ value: cell, rowspan: 1, colspan: 1 })));

            merges.forEach((merge) => {
                const startRow = merge.s.r;
                const startCol = merge.s.c;
                const endRow = merge.e.r;
                const endCol = merge.e.c;
                const rowspan = endRow - startRow + 1;
                const colspan = endCol - startCol + 1;

                if (startRow === 0) { // Merges in headers
                    this.headers[startCol].colspan = colspan;
                    for (let col = startCol + 1; col <= endCol; col++) {
                        this.hiddenHeaders.push(col);
                    }
                }

                for (let row = startRow; row <= endRow; row++) {
                    if (row > 0) { // Merges in rows
                        for (let col = startCol; col <= endCol; col++) {
                            if (row === startRow) {
                                this.rows[row - 1][col].rowspan = rowspan;
                                this.rows[row - 1][col].colspan = colspan;
                            } else {
                                this.rows[row - 1][col] = { ...this.rows[row - 1][col], hidden: true };
                            }
                        }
                    }
                }
            });

            this.groupedRows = this.rows.map(row => row.filter(cell => !cell.hidden));
        },
        exportToExcel() {
            const worksheet = XLSX.utils.aoa_to_sheet(this.rows.map(row => row.map(cell => cell.value)));

            // Menambahkan merges ke worksheet berdasarkan data header dan row
            const merges = [];
            this.headers.forEach((header, index) => {
                if (header.colspan > 1) {
                    merges.push({ s: { r: 0, c: index }, e: { r: 0, c: index + header.colspan - 1 } });
                }
            });
            this.rows.forEach((row, rowIndex) => {
                row.forEach((cell, colIndex) => {
                    if (cell.rowspan > 1 || cell.colspan > 1) {
                        merges.push({
                            s: { r: rowIndex + 1, c: colIndex },
                            e: { r: rowIndex + cell.rowspan, c: colIndex + cell.colspan - 1 }
                        });
                    }
                });
            });
            worksheet['!merges'] = merges;

            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Data");

            // Menentukan nama file
            XLSX.writeFile(workbook, "data_exported.xlsx");
        },
    },
};
</script>

<style scoped>
.c1{
    position: absolute;
    width: 100vw;
    padding: 3%;
}

body {
    width: 100vw;
    padding: 3rem;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table,
th,
td {
    border: 1px solid black;
    text-align: center;
    padding: 5px;
    width: 5rem;
}
</style>
