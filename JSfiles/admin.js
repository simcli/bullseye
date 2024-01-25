
//use a placeholder for the password field no need to even retrieve the password
function buildTable(text) {
    let arr = JSON.parse(text); // get JS Objects
    let html =
        "<table><tr><th>ID</th><th>Customer Name</th><th>Phone</th><th>Credit</th><th>Gold Member?</th></tr>";
    for (let i = 0; i < arr.length; i++) {
        let row = arr[i];
        html += "<tr>";
        html += "<td>" + row.customerNumber + "</td>";
        html += "<td>" + row.customerName + "</td>";
        html += "<td>" + row.phone + "</td>";
        html += "<td>" + row.credit + "</td>";
        html += "<td>" + (row.premiumMember === 1 ? "Yes" : "No") + "</td>";
        html += "</tr>";
    }
    html += "</table>";
    let theTable = document.querySelector("#mainOutput");
    theTable.innerHTML = html;
}


export { buildTable };