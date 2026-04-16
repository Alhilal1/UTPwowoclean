const api = axios.create({
    baseURL: "http://127.0.0.1:8000/api"
});

//  LOAD DATA + TOTAL
function loadData() {
    api.get("/containers")
        .then(res => {
            const data = res.data;

            let total = 0;
            let html = "";

            data.forEach(item => {
                total += item.weight_kg;

                html += `
                    <div class="card">
                        <b>${item.container_id}</b><br>
                        Type: ${item.waste_type}<br>
                        Weight: ${item.weight_kg} kg<br>
                        Status: 
                        <span class="${item.status === 'Active' ? 'status-active' : 'status-archived'}">
                            ${item.status}
                        </span>
                        <br><br>

                        <button class="btn-archive" onclick="archive('${item.container_id}')">
                            Archive
                        </button>

                        <button class="btn-delete" onclick="hapus('${item.container_id}')">
                            Delete
                        </button>
                    </div>
                `;
            });

            document.getElementById("list").innerHTML = html;
            document.getElementById("total").innerText = total;
        })
        .catch(() => {
            document.getElementById("list").innerHTML = "Gagal mengambil data";
        });
}

// CREATE DATA
document.getElementById("form").addEventListener("submit", function (e) {
    e.preventDefault();

    const container_id = document.getElementById("container_id").value;
    const waste_type = document.getElementById("waste_type").value;
    const weight = document.getElementById("weight").value;

    api.post("/containers", {
        container_id: container_id,
        waste_type: waste_type,
        weight_kg: weight,
        status: "Active"
    })
        .then(() => {
            document.getElementById("error").innerText = "";

            // reset form
            document.getElementById("form").reset();

            loadData();
        })
        .catch(err => {
            let msg = "";

            if (err.response?.data?.errors) {
                const errors = err.response.data.errors;

                for (let key in errors) {
                    msg += errors[key][0] + "\n";
                }
            } else if (err.response?.data?.error) {
                msg = err.response.data.error;
            } else {
                msg = "Validasi gagal";
            }

            document.getElementById("error").innerText = msg;
        });
});

// ARCHIVE
function archive(id) {
    api.patch(`/containers/${id}/archive`)
        .then(() => loadData())
        .catch(() => alert("Gagal archive"));
}

// DELETE
function hapus(id) {
    if (confirm("Yakin mau hapus data ini?")) {
        api.delete(`/containers/${id}`)
            .then(() => loadData())
            .catch(() => alert("Gagal delete"));
    }
}

// LOAD AWAL
loadData();