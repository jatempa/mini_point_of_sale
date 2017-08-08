let accountForm = {
  template: `
  <div>
      <div v-if="accountListLength > 0" class="field">
        <div class="control">
          <table class="table">
            <thead>
              <tr>
                <th>No.</th>
                <th>Mesa</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="account in accountList" :key="account.id">
                <td>{{ account.id }}</td>
                <td>{{ account.mesa }}</td>
                <td v-if=" account.status">
                  <span class="tag is-success">Abierta</span>
                </td>
                <td v-else>
                  <span  class="tag is-danger">Cerrada</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>    
      </div>
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
      </div>  
  </div>`,
  data() {
    return {
      mesas: [],
      accountList: []
    }
  },
  mounted() {
    this.fetchTables();
    this.fetchAccounts();
  },
  methods: {
    fetchAccounts () {
      axios.get('/api/accounts')
           .then(response => {
             this.accountList = response.data.accounts;
           })
           .catch(function (error) {
             console.log(error);
           });
    },
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
    accountListLength() {
      return this.accountList.length;
    }
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
                   swal('¡Correcto!', 'Cuenta registrada satisfactoriamente', 'success');
                 }
               })
               .catch(function (error) {
                 console.log(error);
                 swal('Error', 'Esta cuenta no pudo ser registrada en el sistema', 'error')
               });
          // Clean form
          this.cleanForm();
        } else {
          swal('Error', 'Es necesario especificar valores correctos para procesar la cuenta', 'warning');
        }
      },
      closeAccount () {
        axios.defaults.headers.common = {
          'X-Requested-With': 'XMLHttpRequest',
        };

        if(this.$store.state.selectedTable > 0) {
          let accountData = {
            selectedTable: parseInt(this.$store.state.selectedTable)
          };

          axios.put('/api/accounts/close', accountData)
               .then(function (response) {
                 if(response.data === 'success') {
                   swal('¡Correcto!', 'Cuenta cerrada satisfactoriamente', 'success');
                 }
               })
               .catch(function (error) {
                 console.log(error);
                 swal('Error', 'Esta cuenta no pudo ser cerrada en el sistema', 'error')
               });
          // Clean form
          this.cleanForm();
        } else {
          swal('Error', 'Es necesario especificar valores correctos para procesar la cuenta', 'warning');
        }
      },
      cleanForm () {
        this.$store.commit('updateSelectedTable', 0);
      }
    }
});