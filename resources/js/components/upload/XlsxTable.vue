<template>
    <div v-if="table" v-html="table" class="table-responsive" />
</template>

<script>
import SheetTo from "../../mixins/SheetTo.js";

export default {
    mixins: [SheetTo],
    data() {
        return {
            table: null,
        };
    },
    mounted() {
        this._callBack = this.updateTable;
        this.load();
    },
    methods: {
        async load() {
            const {
                utils: { sheet_to_html },
            } = await import("xlsx");
            this._sheet_to_html = sheet_to_html;
            this.loaded = true;
        },
        updateTable(workbook) {
            const ws = workbook.Sheets[this.sheetNameFinder(workbook)];
            let html = this._sheet_to_html(ws, this.options);

            html = html.replace(
                "<table>",
                '<table class="border border-collapse text-center" style="width:100%;">'
            );

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const allCells = doc.querySelectorAll("tr");
            allCells.forEach((row) => {
                const cells = row.querySelectorAll("td, th");
                cells.forEach((cell) => {
                    const cellText = cell.textContent.trim();
                    
                    if (cellText === "") {
                        cell.remove();
                        return;
                    }
                    
                    cell.classList.add("border", "border-gray-300", "p-2");
                    
                    if (["INPUT", "PROSES", "OUTPUT"].includes(cellText)) {
                        cell.classList.add("bg-gray-100", "font-semibold");
                    }
                });
            });

            this.table = doc.body.innerHTML;
        },
    },
};
</script>
