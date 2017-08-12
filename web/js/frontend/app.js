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
            <button type="button" class="btn btn-primary btn-lg" @click="loadMorePendingNotes">
              <i class="fa fa-refresh" aria-hidden="true"></i> Comandas
            </button>
          </form>
        </div>  
      </div>
      <div class="row">
        <div id="site" class="col-md-12 col-lg-12">
          <div class="col-md-4 col-lg-4" v-for="n in getPendingNotes" :key="n.id">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title"><strong>Comanda {{ n.numberNote }}</strong> - Mesero(a) {{ n.fullname }}</h3>
              </div>
              <div class="panel-body">
                <ul>
                  <li v-for="product in n.products" :key="product.id">
                    <h2>{{ product.amount }} - {{ product.product }}</h2>
                  </li>
                </ul>
              </div>
              <div class="panel-footer text-center">
                <form>
                    <button type="button" class="btn btn-success btn-lg" >
                        <i class="fa fa-check" aria-hidden="true"></i> Entregado
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
    this.fetchPendingNotes();
  },
  methods: {
    fetchPendingNotes () {
      axios.get('/api/notes/pending')
           .then(response => {
             this.notes = response.data.notes;
           })
           .catch(function (error) {
              console.log(error);
           });
    },
    loadMorePendingNotes () {
      this.fetchPendingNotes();
    },
    checkoutNote (pendingNote) {
      axios.defaults.headers.common = {
        'X-Requested-With': 'XMLHttpRequest',
      };

      let note = {
        userId: pendingNote.userId,
        productId: pendingNote.productId,
        noteId: pendingNote.noteId,
        noteProductId: pendingNote.noteProductId,
        amount: parseInt(pendingNote.amount)
      };

      axios.put('/notes/checkout/product', note)
           .then(function (response) {
             if(response.data === 'success') {
               pendingNote.status = "Entregado";
               swal('¡Correcto!', 'Producto actualizado satisfactoriamente', 'success');
             }
           })
           .catch(function (error) {
             console.log(error);
             swal('Error', 'Esta venta no ha podido ser registrada en el sistema. No entregue el producto y solicite generen una nueva comanda.', 'error')
           });
    }
  },
  computed: {
    getPendingNotes() {
      return this.notes.filter((n) => n.status === 'Pendiente');
    }
  }
});