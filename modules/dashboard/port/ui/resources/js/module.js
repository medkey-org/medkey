var Dashboard_Module = function () {
    Module.call(this);
};
Dashboard_Module.prototype = Object.create(Module.prototype);
Dashboard_Module.prototype.constructor = Dashboard_Module;

application.setModule({
    'dashboard': new Dashboard_Module()
});