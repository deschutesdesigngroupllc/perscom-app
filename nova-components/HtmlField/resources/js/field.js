import FormField from "./components/FormField";

Nova.booting((app, store) => {
    app.component("index-HtmlField", FormField);
    app.component("detail-HtmlField", FormField);
    app.component("form-HtmlField", FormField);
});
