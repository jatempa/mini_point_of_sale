let notes = {
    template: `
          <div class="content">
            <div class="columns is-mobile is-centered">
              <div class="column">
                <form @submit.prevent="createNote">
                  <div class="field">
                    <label class="label">Número de la mesa</label>
                    <div class="control">
                      <input v-model="tableNumber" class="input" type="number" min="0" max="100" placeholder="Introduce el número de la mesa">
                    </div>
                  </div>
                  <div class="field">
                    <label class="label">Categoría de producto</label>
                    <div class="control">
                      <div class="select">
                         <select v-model="selectedCategory">
                           <option v-for="category in categories" :value="category.id">
                             {{ category.name }}
                           </option>
                         </select>
                      </div>
                    </div>
                  </div>
                  <div v-if="selectedCategory > 0" class="field">
                    <label class="label">Selecciona un producto</label>
                    <div class="control">
                      <div class="select">
                         <select v-model="selectedProduct">
                           <option v-for="product in getProductsByCategory" :value="product.id">
                             {{ product.name }} ($ {{ product.price }})
                           </option>
                         </select>
                      </div>
                    </div>
                  </div>
                  <div v-if="selectedProduct > 0">
                    <div class="field">
                      <label class="label">Cantidad</label>
                      <div class="control">
                        <input v-model="amount" class="input" type="number" min="0" max="100" placeholder="Introduce la cantidad del producto">
                      </div>
                    </div>
                    <div class="field">
                      <h3>Total $ {{ calculateTotal }}</h3>
                    </div>                    
                    <div class="field is-grouped">
                      <div class="control">
                        <button type="submit" class="button is-primary">
                          <span class="icon is-normal">
                            <i class="fa fa-save"></i>
                          </span>
                          <span>Registrar Comanda</span>
                        </button>
                      </div>
                      <div class="control">
                        <button class="button" @click="cleanForm">
                          <span class="icon is-normal">
                            <i class="fa fa-remove"></i>
                          </span>
                          <span>Cancelar</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>              
            </div>
    `,
    data() {
      return {
        tableNumber: 0,
        categories: [],
        products: [],
        amount: 0,
        selectedCategory: 0,
        selectedProduct: 0,
        total: 0
      }
    },
    mounted() {
      this.fetchCategories();
      this.fetchProducts();
      this.getLastNoteNumber();
    },
    methods: {
      fetchCategories: function () {
        axios.get('/api/categories')
          .then(response => {
              this.categories = response.data.categories;
          })
          .catch(function (error) {
              console.log(error);
          });
      },
      fetchProducts: function () {
        axios.get('/api/products')
          .then(response => {
              this.products = response.data.products;
          })
          .catch(function (error) {
              console.log(error);
          });
      },
      getLastNoteNumber: function () {
        axios.get('/api/notes/lastNoteId')
           .then(response => {
               response.data.map((n) => this.$store.state.lastNumberNote = parseInt(n.numberNote) + 1);
               if (!(this.$store.state.lastNumberNote > 0)) {
                   this.$store.state.lastNumberNote = 1;
               }
           })
           .catch(function (error) {
              console.log(error);
               this.$store.state.lastNumberNote = 1;
           });
      },
      createNote () {
        axios.defaults.headers.common = {
            'X-Requested-With': 'XMLHttpRequest',
        };

        if((parseInt(this.selectedProduct) > 0) && (parseInt(this.amount) > 0) && (parseInt(this.total) > 0)) {

          let noteData = {
            numberNote: parseInt(this.$store.state.lastNumberNote),
            product: parseInt(this.selectedProduct),
            amount: parseInt(this.amount),
            total: parseFloat(this.total)
          };

          axios.post('/api/notes/create', noteData)
            .then(function (response) {
                if(response.data === 'success') {
                    swal('¡Correcto!', 'Comanda registrada satisfactoriamente', 'success');
                }
            })
            .catch(function (error) {
                console.log(error);
                swal('Error', 'Esta comanda no pudo ser registrada en el sistema', 'error')
            });
          this.$store.state.lastNumberNote += 1;
          // Clean form
          this.cleanForm();
        } else {
          swal('Error', 'Es necesario especificar valores correctos para procesar la comanda', 'warning');
        }
      },
      cleanForm: function () {
        this.tableNumber = 0;
        this.selectedCategory = 0;
        this.selectedProduct = 0;
        this.amount = 0;
      }
    },
    computed: {
      calculateTotal: function () {
          this.total = parseFloat(this.products.filter((p) => p.id === this.selectedProduct).map((x) => x.price)) * this.amount;
          return this.total > 0 ? this.total : 0;
      },
      getProductsByCategory: function () {
          return this.products.filter((p) => p.category === this.selectedCategory);
      }
    }
}

Vue.use('vuex');

const store = new Vuex.Store({
    state: {
        lastNumberNote: 0
    }
});

new Vue({
  delimiters: ['${', '}'],
  el: 'main',
  store,
  components: { notes }
});