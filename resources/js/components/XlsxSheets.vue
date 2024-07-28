<script>
import { h } from 'vue';

export default {
  inject: ["getWorkbook"],
  data() {
    return {
      innerValue: []
    };
  },
  mounted() {
    this.getWorkbook(this.parseSheets);
  },
  methods: {
    parseSheets(wb) {
      console.log("Sheet names received:", wb.SheetNames);
      this.innerValue = [...wb.SheetNames];
      this.$emit("parsed", [...wb.SheetNames]);
    }
  },
  render() {
    // if (this.$scopedSlots.default) {
    //   return h("div", [
    //     this.$scopedSlots.default({
    //       sheets: this.innerValue
    //     })
    //   ]);
    // }
    // return null;
    if (this.$slots.default) {
      return h("div", [
        this.$slots.default({
          sheets: this.innerValue
        })
      ]);
    }
    return null;
  }
};
</script>
