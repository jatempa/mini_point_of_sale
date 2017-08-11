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
            <button type="button" class="btn btn-primary btn-lg" @click="loadPendingNotes">
              <i class="fa fa-refresh" aria-hidden="true"></i> Comandas
            </button>
          </form>
        </div>  
      </div>
      <div class="row">
        <div id="site" class="col-md-12 col-lg-12">
          <div class="col-md-4 col-lg-4" v-for="note in notes" :key="note.id">
            <div class="panel panel-primary">
              <div class="panel-heading">
                  <h3 class="panel-title"><strong>Comanda {{ note.numberNote }}</strong> - {{ note.waiter }}</h3>
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
                  <button type="button" class="btn btn-success btn-lg">
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
    this.getPendingNotes();
  },
  methods: {
    getPendingNotes () {
      axios.get('/api/notes/pending')
           .then(response => {
             this.notes = response.data.notes;
           })
           .catch(function (error) {
              console.log(error);
           });
    },
    loadPendingNotes () {
      this.getPendingNotes();
    }
  }
});