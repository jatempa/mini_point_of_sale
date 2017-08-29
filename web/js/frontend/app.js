new Vue({
  delimiters: ['${', '}'],
  el: 'main',
  data() {
    return {
      notes: []
    }
  },
  mounted() {
    this.fetchPendingNotes();
  },
  methods: {
    fetchPendingNotes () {
      axios.get('/api/notes/pending/today')
           .then(response => {
              this.notes = response.data.notes;
           })
           .catch(function (error) {
              console.log(error);
           });
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
             swal('Error', 'Esta venta no ha podido ser registrada en el sistema. Una causa posible es la falta de productos en stock de algun producto.', 'error')
           });
    }
  },
  computed: {
    getPendingNotes() {
      return this.notes.filter((n) => n.status === 'Pendiente');
    }
  }
});