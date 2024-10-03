<script setup>
import { ref, computed } from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
  initialProductionCost: { type: Number },
  initialShippingToKurasi: { type: Number },
  initialShippingToCustomer: { type: Number },
  initialAnotherCost: { type: Number },
  initialLength: { type: Number },
  initialWidth: { type: Number },
  initialHeight: { type: Number },
});

const productionCost = ref(props.initialProductionCost);
const shippingToKurasi = ref(props.initialShippingToKurasi);
const shippingToCustomer = ref(props.initialShippingToCustomer);
const anotherCost = ref(props.initialAnotherCost);
const localPrice = ref(0);
const aboardPrice = ref(0);

const panjang = ref(props.initialLength);
const lebar = ref(props.initialWidth);
const tinggi = ref(props.initialHeight);

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(value);
};

const formatNumberWithCommas = (value) => {
  if (!value && value !== 0) return '';
  return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

const parseNumber = (value) => {
  if (typeof value === 'string') {
    // Remove commas and replace period for float conversion
    return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
  }
  return typeof value === 'number' ? value : 0;
};

const formattedProductionCost = computed({
  get() {
    return formatNumberWithCommas(productionCost.value);
  },
  set(value) {
    productionCost.value = parseNumber(value);
  }
});

const formattedShippingToKurasi = computed({
  get() {
    return formatNumberWithCommas(shippingToKurasi.value);
  },
  set(value) {
    shippingToKurasi.value = parseNumber(value);
  }
});

const formattedShippingToCustomer = computed({
  get() {
    return formatNumberWithCommas(shippingToCustomer.value);
  },
  set(value) {
    shippingToCustomer.value = parseNumber(value);
  }
});

const formattedAnotherCost = computed({
  get() {
    return formatNumberWithCommas(anotherCost.value);
  },
  set(value) {
    anotherCost.value = parseNumber(value);
  }
});

const calculatePrices = () => {
  const prodCost = parseNumber(formattedProductionCost.value);
  const shipToKurasi = parseNumber(formattedShippingToKurasi.value);
  const shipToCustomerVal = parseNumber(formattedShippingToCustomer.value);
  const anotherCostVal = parseNumber(formattedAnotherCost.value);

  if (isNaN(prodCost) || isNaN(shipToKurasi) || isNaN(shipToCustomerVal)) {
    alert("Please enter valid numbers for production cost, shipping to Kurasi, and shipping to customer.");
    return;
  }

  //  Local Price
  localPrice.value = Math.ceil((prodCost + anotherCostVal) / (1 - (0.33 + 0.1)) / 10000) * 10000;

  //  Aboard Price
  const totalCost = prodCost + shipToCustomerVal + shipToKurasi + anotherCostVal;
  aboardPrice.value = Math.ceil((totalCost * 2.5 - shipToCustomerVal) / 10000) * 10000;
};

const volume = computed(() => {
  const parsedPanjang = parseNumber(panjang.value);
  const parsedLebar = parseNumber(lebar.value);
  const parsedTinggi = parseNumber(tinggi.value);
  
  const calculatedVolume = ((parsedPanjang + 6) * (parsedLebar + 6) * (parsedTinggi + 6)) / 6000;
  if (parsedPanjang <= 0 || parsedLebar <= 0 || parsedTinggi <= 0) {
    return 0; 
  }

  return calculatedVolume.toFixed(3);
});
</script>

<template>
    <Head title="Kalkulator Harga Produk" />
    <AuthenticatedLayout>
        <Breadcrumb :title="'Kalkulator Harga Produk'" :breadcrumbs="[]" />
        <div class="space-y-4">
            <div class="relative bg-white dark:bg-slate-800 shadow sm:rounded-lg p-6">
                <div class="flex flex-col space-y-4">
                    <div class="input-group">
                        <label for="productionCost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Produksi</label>
                        <input type="text" 
                               v-model="formattedProductionCost" 
                               id="productionCost" 
                               class="mt-1 w-full text-left p-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                               placeholder="Masukkan harga produksi" />
                    </div>

                    <div class="input-group">
                        <label for="shippingToKurasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ongkir to Kurasi</label>
                        <input type="text" 
                               v-model="formattedShippingToKurasi" 
                               id="shippingToKurasi" 
                               class="mt-1 w-full text-left p-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                               placeholder="Isi jika ingin kalkulasi produk untuk dijual di luar negeri" />
                    </div>

                    <div class="input-group">
                        <label for="shippingToCustomer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ongkir Kurasi to Customer (US)</label>
                        <input type="text" 
                               v-model="formattedShippingToCustomer" 
                               id="shippingToCustomer" 
                               class="mt-1 w-full text-left p-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                               placeholder="Isi jika ingin kalkulasi produk untuk dijual di luar negeri" />
                    </div>

                    <div class="input-group">
                        <label for="anotherCost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Biaya lainnya (Opsional)</label>
                        <input type="text" 
                               v-model="formattedAnotherCost" 
                               id="anotherCost" 
                               class="mt-1 w-full text-left p-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                               placeholder="Masukkan biaya lainnya (Opsional)" />
                    </div>

                    <div class="flex space-x-4">
                        <div class="input-group">
                            <label for="panjang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Panjang</label>
                            <input type="number" 
                                v-model="panjang" 
                                id="panjang" 
                                class="mt-1 w-full text-left p-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                                placeholder="Masukkan Panjang" />
                        </div>

                        <div class="input-group">
                            <label for="lebar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lebar</label>
                            <input type="number" 
                                v-model="lebar" 
                                id="lebar" 
                                class="mt-1 w-full text-left p-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                                placeholder="Masukkan Lebar" />
                        </div>

                        <div class="input-group">
                            <label for="tinggi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tinggi</label>
                            <input type="number" 
                                v-model="tinggi" 
                                id="tinggi" 
                                class="mt-1 w-full text-left p-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                                placeholder="Masukkan Tinggi" />
                        </div>

                    </div>

                    <div class="mt-4">
                        <PrimaryButton @click="calculatePrices">Kalkulasi</PrimaryButton>
                    </div>

                    <div class="results mt-4">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hasil Kalkukasi</h2><br>
                        <p>Harga Jual Lokal: <br>{{ formatCurrency(localPrice) }}</p><br>
                        <p>Harga Jual Luar: <br>{{ formatCurrency(aboardPrice) }}</p><br>
                        <p>Volume Produk: <br>{{ volume }} gram</p><br>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
