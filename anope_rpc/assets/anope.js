(function ($) {
	if (!$) return;
	var L = window.ANOPE_I18N || {};
	function t(k) { return L[k] || k; }
	var dtLang = {
		search: t("Search:"), lengthMenu: t("_MENU_ per page"), info: t("_START_–_END_ of _TOTAL_"),
		infoEmpty: t("0 results"), infoFiltered: t("(filtered from _MAX_)"), zeroRecords: t("No results"),
		paginate: { first: "«", last: "»", next: "›", previous: "‹" }
	};
	var flagFilter = "", serverFilter = "";

	$(function () {
		if ($.fn.DataTable) {
			// Custom filter based on the rows' data-flags attribute and the server
			$.fn.dataTable.ext.search.push(function (settings, data, idx) {
				var row = settings.aoData[idx].nTr;
				if (flagFilter && (" " + ($(row).attr("data-flags") || "") + " ").indexOf(" " + flagFilter + " ") < 0) return false;
				if (serverFilter && settings.nTable.id === "anope-users" && data[4] !== serverFilter) return false;
				return true;
			});
			$("table.anope-dt").each(function () {
				var order = this.id === "anope-channels" ? [[1, "desc"]] : this.id === "anope-accounts" ? [[1, "desc"]] : [[0, "asc"]];
				$(this).DataTable({ pageLength: 50, order: order, language: dtLang, autoWidth: false });
			});
		}
		$("input[name=f]").on("change", function () {
			flagFilter = this.value; $("table.anope-dt").DataTable().draw();
		});
		$("#anope-server-filter").on("change", function () {
			serverFilter = this.value; $("#anope-users").DataTable().draw();
		});
		$(".anope-filter-link").on("click", function (e) {
			e.preventDefault();
			flagFilter = flagFilter ? "" : $(this).data("filter");
			$(this).text(flagFilter ? t("Show all channels") : t("Show only channels without a topic"));
			$("table.anope-dt").DataTable().draw();
		});

		// Explorer: click on a method
		$(".anope-pick").on("click", function (e) { e.preventDefault(); $("#anope-method").val($(this).data("m")).focus(); });
		$("#anope-copy").on("click", function () {
			navigator.clipboard && navigator.clipboard.writeText($("#anope-out").text());
			$(this).text(t("Copied ✓"));
		});

		// Dashboard auto-refresh
		var key = "anope_autorefresh", cb = $("#anope-autorefresh"), timer = null;
		function arm() { clearTimeout(timer); if (cb.is(":checked")) timer = setTimeout(function () { location.reload(); }, 60000); }
		try { cb.prop("checked", localStorage.getItem(key) === "1"); } catch (e) {}
		cb.on("change", function () { try { localStorage.setItem(key, this.checked ? "1" : "0"); } catch (e) {} arm(); });
		arm();
	});
})(window.jQuery);
