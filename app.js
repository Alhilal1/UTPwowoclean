const BASE_URL =
"http://127.0.0.1:8000/api/v1";

// AXIOS INSTANCE
const api = axios.create({
    baseURL: BASE_URL
});

// AUTO LOGIN CHECK
if(localStorage.getItem("token")){
    showApp();
}

// LOGIN
async function login(){

    try{

        const response = await api.post(
            "/login",
            {
                email:
                    document.getElementById("email").value,

                password:
                    document.getElementById("password").value
            }
        );

        // SAVE TOKEN
        localStorage.setItem(
            "token",
            response.data.token
        );

        alert("Login berhasil");

        showApp();

    }catch(err){

        console.log(err.response);

        document.getElementById(
            "loginError"
        ).innerText =
            err.response.data.message;
    }
}

// SHOW APP
function showApp(){

    document.getElementById(
        "loginSection"
    ).style.display = "none";

    document.getElementById(
        "appSection"
    ).style.display = "block";

    loadData();
}

// LOGOUT
function logout(){

    localStorage.removeItem("token");

    location.reload();
}

// AUTH HEADER
function authHeader(){

    return {
        headers:{
            Authorization:
                `Bearer ${localStorage.getItem("token")}`
        }
    };
}

// LOAD DATA
async function loadData(){

    try{

        const response = await api.get(
            "/gateway/containers",
            authHeader()
        );

        const data = response.data;

        let html = "";

        let total = 0;

        data.forEach(container => {

            total += container.weight_kg;

            html += `
                <div class="card">

                    <h3>${container.container_id}</h3>

                    <p>
                        Waste Type:
                        ${container.waste_type}
                    </p>

                    <p>
                        Weight:
                        ${container.weight_kg} KG
                    </p>

                    <p>
                        Status:
                        ${container.status}
                    </p>

                    <button onclick="archive(${container.id})">
                        Archive
                    </button>

                    <button onclick="hapus(${container.id})">
                        Delete
                    </button>

                </div>
            `;
        });

        document.getElementById(
            "containerList"
        ).innerHTML = html;

        document.getElementById(
            "totalWeight"
        ).innerText = total;

    }catch(err){

        console.log(err);
    }
}

// CREATE CONTAINER
document.getElementById(
    "containerForm"
).addEventListener(
    "submit",
    async function(e){

        e.preventDefault();

        try{

            // CLEAR ERROR
            document.getElementById(
                "error"
            ).innerHTML = "";

            await api.post(
                "/gateway/containers",
                {
                    container_id:
                        document.getElementById("container_id").value,

                    waste_type:
                        document.getElementById("waste_type").value,

                    weight_kg:
                        document.getElementById("weight_kg").value,

                    status:
                        document.getElementById("status").value
                },
                authHeader()
            );

            alert("Container berhasil ditambah");

            document.getElementById(
                "containerForm"
            ).reset();

            loadData();

        }catch(err){

            // VALIDATION ERROR
            if(err.response.data.errors){

                let errors =
                    err.response.data.errors;

                let message = "";

                for(let key in errors){

                    message +=
                        errors[key][0] + "<br>";
                }

                document.getElementById(
                    "error"
                ).innerHTML = message;

            }else{

                // OTHER ERROR
                document.getElementById(
                    "error"
                ).innerHTML =
                    err.response.data.message;
            }
        }
    }
);

// ARCHIVE
async function archive(id){

    try{

        await api.patch(
            `/gateway/containers/${id}/archive`,
            {},
            authHeader()
        );

        alert(
            "Container berhasil diarchive"
        );

        loadData();

    }catch(err){

        alert(
            err.response.data.message
        );
    }
}

// DELETE
async function hapus(id){

    if(confirm("Yakin ingin hapus?")){

        try{

            await api.delete(
                `/gateway/containers/${id}`,
                authHeader()
            );

            alert(
                "Container berhasil dihapus"
            );

            loadData();

        }catch(err){

            alert(
                err.response.data.message
            );
        }
    }
}