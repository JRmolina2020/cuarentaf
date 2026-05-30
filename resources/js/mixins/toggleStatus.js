import Swal from "sweetalert2";
import axios from "axios";

export default async function toggleStatus({ id, currentStatus, entity = "elemento", url, callback = null }) {
    const action = currentStatus ? "bloquear" : "desbloquear";

    const result = await Swal.fire({
        title: `¿Seguro que deseas ${action} este ${entity}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, continuar",
        cancelButtonText: "Cancelar",
    });

    if (result.isConfirmed) {
        try {
            const response = await axios.put(`${url}/${id}/toggle-status`);
            await Swal.fire({
                icon: "success",
                title: response.data.message,
                timer: 1500,
                showConfirmButton: false,
            });
            if (typeof callback === "function") {
                callback();
            }
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Error al cambiar estado",
                text: error.response?.data?.message || "Intenta más tarde.",
            });
        }
    }
}
