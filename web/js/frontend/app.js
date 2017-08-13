new Vue({
  el: 'main',
  data() {
    return {
      notes: [],
      initialDate: '',
      finalDate: ''
    }
  },
  template: `
  <section class="col-md-12 col-lg-12">
      <div class="row">
        <div class="col-md-9 col-lg-9">
          <div class="form-group">
            <label>Fecha Inicial</label>
            <input type="date" v-model="initialDate" />
          </div>
          <div class="form-group">
            <label>Fecha Final</label>
            <input type="date" v-model="finalDate" />
          </div>
        </div>
        <div class="col-md-3 col-lg-3">
          <form style="margin-bottom: 25px">
            <button type="button" class="btn btn-primary btn-lg" @click="loadMorePendingNotes">
              <i class="fa fa-refresh" aria-hidden="true"></i> Actualizar Comandas
            </button>
          </form>
        </div>  
      </div>
      <div class="row">
        <div class="col-md-12 col-lg-12">
          <div class="col-md-4 col-lg-4" v-for="n in getPendingNotes" :key="n.id">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title"><strong>Mesero {{ n.userId }} </strong> {{ n.fullname }} / Comanda {{ n.numberNote }}</h3>
              </div>
              <div class="panel-body">
                <ul>
                  <li v-for="product in n.products" :key="product.id">
                    <h2>{{ product.amount }} {{ product.product }}</h2>
                    <h3>{{ product.category }} $ {{ product.price }}</h3>
                  </li>
                </ul>
              </div>
              <div class="panel-footer text-center">
                <form>
                  <button type="button" class="btn btn-success btn-lg" @click.prevent="checkoutNote(n)">
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
      if (this.initialDate !== "" && this.finalDate !== "") {
        axios.get('/api/notes/pending/' + this.initialDate + '/' + this.finalDate)
            .then(response => {
                this.notes = response.data.notes;
            })
            .catch(function (error) {
                console.log(error);
            });
      } else {
        axios.get('/api/notes/pending')
            .then(response => {
                this.notes = response.data.notes;
            })
            .catch(function (error) {
                console.log(error);
            });
      }
    },
    loadMorePendingNotes () {
      this.fetchPendingNotes();
    },
    checkoutNote (pendingNote) {
      axios.defaults.headers.common = {
        'X-Requested-With': 'XMLHttpRequest',
      };

      let note = {
        numberNote: pendingNote.numberNote,
        products: pendingNote.products,
        userId: pendingNote.userId
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