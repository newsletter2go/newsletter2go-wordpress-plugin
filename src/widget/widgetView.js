!function (e, t, n, c, r, a, i) {
    e.Newsletter2GoTrackingObject = r, e[r] = e[r] || function () {
        (e[r].q = e[r].q || []).push(arguments)
    }, e[r].l = 1 * new Date, a = t.createElement(n), i = t.getElementsByTagName(n)[0], a.async = 1, a.src = c, i.parentNode.insertBefore(a, i)
}(window, document, "script", "https://static.newsletter2go.com/utils.js", "n2g");
n2g("create", formUniqueCode);

if (uniqueId && !popup) {
    n2gParams.push("'" + uniqueId+ "'");
}
let n2gFun = "n2g(" + n2gParams.join(", ") + ")";
eval(n2gFun);
