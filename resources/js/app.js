import "./bootstrap";

Alpine.data("dropdown", () => ({
    open: false,

    toggle() {
        this.open = !this.open;
    },
}));
console.log("this is testing");
