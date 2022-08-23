<script>
export default {
    mixins: [Laraform],
    created() {
        this.on("response", (response) => {
            this.$emit("response", response, this.form$);
        });
    },
};
</script>
