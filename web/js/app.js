let product = {
    template: `
    <div>
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
      <div v-if="selectedProduct > 0" class="field">
        <div class="field">
          <label class="label">Cantidad</label>
            <div class="control">
              <input v-model="amount" class="input" type="number" min="0" max="100" placeholder="Introduce la cantidad del producto">
            </div>
        </div>
        <div class="field">
          <h3>Total $ {{ calculateTotal }}</h3>
        </div>      
      </div>
    </div>                   
    `,
    mounted() {
      this.fetchCategories();
      this.fetchProducts();
    },
    methods: {
      fetchCategories: function () {
        axios.get('/api/categories')
             .then(response => {
               this.$store.commit('updateCategories', response.data.categories);
             })
             .catch(function (error) {
               console.log(error);
             });
      },
      fetchProducts: function () {
        axios.get('/api/products')
             .then(response => {
               this.$store.commit('updateProducts', response.data.products);
             })
             .catch(function (error) {
               console.log(error);
             });
      }
    },
    computed: {
      selectedCategory: {
        get () {
          return this.$store.state.selectedCategory
        },
        set (selectedCategory) {
          this.$store.commit('updateSelectedCategory', selectedCategory)
        }
      },
      selectedProduct: {
        get () {
          return this.$store.state.selectedProduct
        },
        set (selectedProduct) {
          this.$store.commit('updateSelectedProduct', selectedProduct)
        }
      },
      amount: {
        get () {
          return this.$store.state.amount
        },
        set (amount) {
          this.$store.commit('updateAmount', amount)
        }
      },
      categories () {
        return this.$store.state.categories;
      },
      products () {
        return this.$store.state.products;
      },
      calculateTotal () {
        return this.$store.getters.calculateTotal;
      },
      getProductsByCategory () {
        return this.$store.getters.getProductsByCategory;
      }
    }
};

let note = {
    template: `
      <div class="field">
        <label class="label">Número de la mesa</label>
        <div class="control">
          <input v-model="tableNumber" class="input" type="number" min="0" max="100" placeholder="Introduce el número de la mesa">
        </div>
      </div>
    `,
    computed: {
      tableNumber: {
        get () {
          return this.$store.state.tableNumber
        },
        set (tableNumber) {
          this.$store.commit('updateTableNumber', tableNumber)
        }
      }
    }
};

Vue.use('vuex');

const store = new Vuex.Store({
    state: {
      lastNumberNote: 0,
      tableNumber: 0,
      categories: [],
      products: [],
      selectedCategory: 0,
      selectedProduct: 0,
      amount: 0,
      total: 0
    },
    mutations: {
      updateTableNumber(state, tableNumber) {
        state.tableNumber = tableNumber
      },
      updateCategories(state, categories) {
        state.categories = categories
      },
      updateProducts(state, products) {
        state.products = products
      },
      updateSelectedCategory(state, selectedCategory) {
        state.selectedCategory = selectedCategory
      },
      updateSelectedProduct(state, selectedProduct) {
        state.selectedProduct = selectedProduct
      },
      updateAmount(state, amount) {
        state.amount = amount
      },
      updateTotal(state, total) {
        state.total = total
      }
    },
    getters: {
      calculateTotal: state => {
        state.total = parseFloat(state.products.filter((p) => p.id === state.selectedProduct).map((x) => x.price)) * state.amount;
        return state.total > 0 ? state.total : 0;
      },
      getProductsByCategory: state => {
        return state.products.filter((p) => p.category === state.selectedCategory);
      }
    }
});

new Vue({
  delimiters: ['${', '}'],
  el: 'main',
  store,
  components: { note, product },
  mounted() {
    this.getLastNoteNumber();
  },
  methods: {
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

      if((parseInt(this.$store.state.selectedProduct) > 0) && (parseInt(this.$store.state.amount) > 0) && (parseInt(this.$store.state.total) > 0)) {
        let noteData = {
          numberNote: parseInt(this.$store.state.lastNumberNote),
          product: parseInt(this.$store.state.selectedProduct),
          amount: parseInt(this.$store.state.amount),
          total: parseFloat(this.$store.state.total)
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
      this.$store.commit('updateTableNumber', 0);
      this.$store.commit('updateSelectedCategory', 0);
      this.$store.commit('updateSelectedProduct', 0);
      this.$store.commit('updateAmount', 0);
      this.$store.commit('updateTotal', 0);
    }
  }
});