import "./bootstrap";

Alpine.data("dropdown", () => ({
    open: false,

    toggle() {
        this.open = !this.open;
    },
}));
