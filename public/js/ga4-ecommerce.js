(function () {
    var PURCHASE_PREFIX = "ga4_purchase_";

    function event(name, payload) {
        try {
            if (typeof window.gtag !== "function") return;

            var data = payload && typeof payload === "object" ? payload : {};

            if (name === "purchase") {
                var id = data.transaction_id;
                if (!id) return;

                var key = PURCHASE_PREFIX + String(id);
                try {
                    if (window.sessionStorage.getItem(key)) return;
                    window.sessionStorage.setItem(key, "1");
                } catch (storageError) {}
            }

            window.gtag("event", name, data);
        } catch (e) {}
    }

    window.ezoneGa4 = { event: event };
})();
