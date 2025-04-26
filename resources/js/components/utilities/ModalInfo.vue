<template>
    <div>
        <!-- Button trigger modal -->
        <button
            type="button"
            class="btn btn-primary btn-sm"
            data-toggle="modal"
            :data-target="'#model' + cod"
        >
            Info
        </button>

        <!-- Modal -->
        <template>
            <div
                class="modal fade"
                :id="'model' + cod"
                tabindex="-1"
                role="dialog"
                :aria-labelledby="'modalTitle' + cod"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content shadow rounded-lg border-0">
                        <div class="modal-header bg-light border-bottom-0">
                            <h5
                                class="modal-title font-weight-bold text-dark"
                                :id="'modalTitle' + cod"
                            >
                                Información del producto
                            </h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Cerrar"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body px-4 py-3 text-secondary">
                            <slot name="body">
                                <p>No se ha proporcionado contenido.</p>
                            </slot>
                        </div>

                        <div
                            class="modal-footer bg-light border-top-0 px-4 py-2"
                        >
                            <button
                                type="button"
                                class="btn btn-outline-secondary btn-sm rounded-pill"
                                data-dismiss="modal"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
<script>
export default {
    name: "ModalInfo",
    props: {
        cod: {
            type: Number,
            required: true,
        },
    },
};
</script>
