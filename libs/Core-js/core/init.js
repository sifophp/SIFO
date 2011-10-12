$LAB.setOptions(
{
	AlwaysPreserveOrder: false,
	UsePreloading: true,
	UseLocalXHR: true,
	UseCachePreload: true,
	AllowDuplicates: false,
	AppendTo: "head",
	BasePath: ""
});
$(document).ready(function() {
	var CP = CORE.behaviour.page;
	if (typeof CP[document.body.id] != "undefined")
	{
		CP[document.body.id]();
	} else {
		CP.unset();
	}
});