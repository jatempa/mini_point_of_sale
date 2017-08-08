let accountForm = {
  template: `
  <div class="field">
    <label class="label">Selecciona una mesa</label>
    <div class="control">
      <div class="select">
        <select v-model="selectedTable">
          <option v-for="mesa in mesas" :value="mesa.id">
            {{ mesa.name }}
          </option>
        </select>
      </div>
    </div>
  </div>`,
  data() {
    return {
      mesas: []
    }
  },
  mounted() {
    this.fetchTables();
  },
  methods: {
    fetchTables () {
      axios.get('/api/tables')
           .then(response => {
             this.mesas = response.data.mesas;
           })
           .catch(function (error) {
             console.log(error);
           });
    }
  },
  computed: {
    selectedTable: {
      get () {
        return this.$store.state.selectedTable
      },
      set (value) {
        this.$store.commit('updateSelectedTable', value)
      }
    },
  }
};

Vue.use('vuex');

const store = new Vuex.Store({
  state: {
    selectedTable: 0
  },
  mutations: {
    updateSelectedTable(state, selectedTable) {
      state.selectedTable = selectedTable;
    }
  }
});

new Vue({
    delimiters: ['${', '}'],
    el: 'main',
    store,
    components: { accountForm },
    methods: {
      createAccount () {
        axios.defaults.headers.common = {
          'X-Requested-With': 'XMLHttpRequest',
        };

        if(this.$store.state.selectedTable > 0) {
          let accountData = {
            selectedTable: parseInt(this.$store.state.selectedTable)
          };

          axios.post('/api/accounts/create', accountData)
               .then(function (response) {
                 if(response.data === 'success') {
                   swal('¡Correcto!', 'Comanda registrada satisfactoriamente', 'success');
                 }
               })
               .catch(function (error) {
                 console.log(error);
                 swal('Error', 'Esta comanda no pudo ser registrada en el sistema', 'error')
               });
          // Clean form
          this.cleanForm();
        } else {
          swal('Error', 'Es necesario especificar valores correctos para procesar la comanda', 'warning');
        }
      },
      cleanForm () {
        this.$store.commit('updateSelectedTable', 0);
      }
    }
});