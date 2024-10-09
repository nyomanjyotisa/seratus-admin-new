<script setup>
import { ref, computed, watchEffect } from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const localPrice = ref(0);
const aboardPrice = ref(0);

const panjang = ref('');
const lebar = ref('');
const tinggi = ref('');

const form = ref({
  productionCost: (''),
  shippingToKurasi: (''),
  shippingToCustomer: (''),
  anotherCost: (''),
});

const formatCurrency = (value) => {
  const formattedValue = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(value);
  
  return formattedValue.replace(/\./g, ',');
};

const formatNumber = (value) => {
  if (!value) return "";
  return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

const unformatNumber = (value) => {
  if (!value) return "";
  return value.replace(/[^0-9]/g, ''); 
};

const parseNumber = (value) => {
  if (typeof value === 'string') {
    return parseFloat(value.replace(/,/g, '')) || 0;
  }
  return typeof value === 'number' ? value : 0;
};

const calculatePrices = () => {
  const prodCost = parseNumber(form.value.productionCost);
  const shipToKurasi = parseNumber(form.value.shippingToKurasi);
  const shipToCustomerVal = parseNumber(form.value.shippingToCustomer);
  const anotherCostVal = parseNumber(form.value.anotherCost);

  if (isNaN(prodCost) || isNaN(shipToKurasi) || isNaN(shipToCustomerVal) || isNaN(anotherCostVal)) {
    alert("Please enter valid numbers for production cost, shipping to Kurasi, and shipping to customer.");
    return;
  }

  // Local Price calculation
  localPrice.value = Math.ceil((prodCost + anotherCostVal) / (1 - (0.33 + 0.1)) / 10000) * 10000;

  // Aboard Price calculation
  const totalCost = prodCost + shipToKurasi + shipToCustomerVal + anotherCostVal;
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

const handleInput = (field) => (event) => {
  let input = event.target.value.replace(/[^\d,\.]/g, '');
  form.value[field] = unformatNumber(input);
};

const handleProdInput = handleInput('productionCost');
const handleShipKurasiInput = handleInput('shippingToKurasi');
const handleShiptoCustomerInput = handleInput('shippingToCustomer');
const handleAnotherCostInput = handleInput('anotherCost');

const productionCostDisplay = ref("");
const shippingToKurasiDisplay = ref('');
const shippingToCustomerDisplay = ref('');
const anotherCostDisplay = ref('');

watchEffect(() => {
  productionCostDisplay.value = formatNumber(form.value.productionCost);
  shippingToKurasiDisplay.value = formatNumber(form.value.shippingToKurasi);
  shippingToCustomerDisplay.value = formatNumber(form.value.shippingToCustomer);
  anotherCostDisplay.value = formatNumber(form.value.anotherCost);
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
                               v-model="productionCostDisplay" 
                               id="productionCost" 
                               class="mt-1 w-full text-left p-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                               @input="handleProdInput"
                               placeholder="Masukkan harga produksi" />
                    </div>

                    <div class="input-group">
                        <label for="shippingToKurasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ongkir to Kurasi</label>
                        <input type="text" 
                               v-model="shippingToKurasiDisplay" 
                               id="shippingToKurasi" 
                               class="mt-1 w-full text-left p-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                               @input="handleShipKurasiInput"
                               placeholder="Isi jika ingin kalkulasi produk untuk dijual di luar negeri" />
                    </div>

                    <div class="input-group">
                        <label for="shippingToCustomer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ongkir Kurasi to Customer (US)</label>
                        <input type="text" 
                               v-model="shippingToCustomerDisplay" 
                               id="shippingToCustomer" 
                               class="mt-1 w-full text-left p-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                               @input="handleShiptoCustomerInput"
                               placeholder="Isi jika ingin kalkulasi produk untuk dijual di luar negeri" />
                    </div>

                    <div class="input-group">
                        <label for="anotherCost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Biaya lainnya (Opsional)</label>
                        <input type="text" 
                               v-model="anotherCostDisplay" 
                               id="anotherCost" 
                               class="mt-1 w-full text-left p-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                               @input="handleAnotherCostInput"
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
