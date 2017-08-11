new Vue({
  el: 'main',
  data() {
    return {
      notes: []
    }
  },
  template: `
  <section class="col-md-12 col-lg-12">
      <div class="row">
        <div class="col-md-2 col-md-offset-10 col-lg-2 col-lg-offset-10">
          <form>
            <button type="button" class="btn btn-primary btn-lg" @click="loadMoreCancelingNotes">
              <i class="fa fa-refresh" aria-hidden="true"></i> Comandas
            </button>
          </form>
        </div>  
      </div>
      <div class="row">
        <div id="site" class="col-md-12 col-lg-12">
          <div class="col-md-4 col-lg-4" v-for="note in getCancelingNotes" :key="note.id"">
            <div class="panel panel-default">
              <div class="panel-heading">
                  <h3 class="panel-title"><strong>Comanda {{ note.numberNote }}</strong> - Mesero(a) {{ note.waiter }}</h3>
              </div>
              <div class="panel-body">
                <h2>
                  {{ note.amount }} {{ note.product }}
                </h2>
                <h3>
                  {{ note.category }}
                </h3>
              </div>
              <div class="panel-footer text-center">
                <form>
                  <button type="button" class="btn btn-danger btn-lg" @click="checkinNote(note)">
                    <i class="fa fa-remove" aria-hidden="true"></i> Cancelar Comanda
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
  </section>
  `,
  mounted() {
    this.fetchCancelingNotes();
  },
  methods: {
    fetchCancelingNotes () {
      axios.get('/api/notes/canceling')
           .then(response => {
             this.notes = response.data.notes;
           })
           .catch(function (error) {
              console.log(error);
           });
    },
    loadMoreCancelingNotes () {
      this.fetchCancelingNotes();
    },
    checkinNote (cancelingNote) {
      axios.defaults.headers.common = {
        'X-Requested-With': 'XMLHttpRequest',
      };

      let note = {
        userId: cancelingNote.userId,
        productId: cancelingNote.productId,
        noteId: cancelingNote.noteId,
        noteProductId: cancelingNote.noteProductId,
        amount: parseInt(cancelingNote.amount)
      };

      axios.put('/notes/checkin/product', note)
           .then(function (response) {
             if(response.data === 'success') {
               cancelingNote.status = "Cancelado";
               swal('¡Correcto!', 'El producto ha sido cancelado satisfactoriamente', 'success');
             }
           })
           .catch(function (error) {
             console.log(error);
             swal('Error en el sistema', 'Este producto no ha podido ser cancelado correctamente.', 'error')
           });
    }
  },
  computed: {
    getCancelingNotes() {
      return this.notes.filter((n) => n.status === "Entregado");
    }
  }
});