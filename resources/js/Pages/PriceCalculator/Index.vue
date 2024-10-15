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
const berat = ref('');

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

const calculateVolume = (length, width, height, divisor, weight) => {
  const parsedLength = parseNumber(length);
  const parsedWidth = parseNumber(width);
  const parsedHeight = parseNumber(height);
  const parsedWeight = parseNumber(weight);

  const calculatedVolume = (((parsedLength + 6) * (parsedWidth + 6) * (parsedHeight + 6)) / divisor)*1000;
  if (parsedLength <= 0 || parsedWidth <= 0 || parsedHeight <= 0) {
    return 0;
  }

  if (parsedWeight > calculatedVolume) {
    return parsedWeight;
  }

  return calculatedVolume.toFixed(0);
};

const volume = computed(() => {
  return calculateVolume(panjang.value, lebar.value, tinggi.value, 6000, berat.value);
});

const aboardVolume = computed(() => {
  return calculateVolume(panjang.value, lebar.value, tinggi.value, 5000, berat.value);
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
                        <div class="relative mt-1">
                            <input type="text" 
                                  v-model="productionCostDisplay" 
                                  id="productionCost" 
                                  class="w-full pl-8 pr-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                                  @input="handleProdInput"
                                  placeholder="Masukkan harga produksi" />
                            <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                                Rp
                            </span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="shippingToKurasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ongkir to Kurasi</label>
                        <div class="relative mt-1">
                          <input type="text" 
                               v-model="shippingToKurasiDisplay" 
                               id="shippingToKurasi" 
                               class="w-full pl-8 pr-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                               @input="handleShipKurasiInput"
                               placeholder="Isi jika ingin kalkulasi produk untuk dijual di luar negeri" />
                          <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                              Rp
                          </span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="shippingToCustomer" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ongkir Kurasi to Customer (US)</label>
                        <div class="relative mt-1">
                          <input type="text" 
                               v-model="shippingToCustomerDisplay" 
                               id="shippingToCustomer" 
                               class="w-full pl-8 pr-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                               @input="handleShiptoCustomerInput"
                               placeholder="Isi jika ingin kalkulasi produk untuk dijual di luar negeri" />
                          <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                              Rp
                          </span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="anotherCost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Biaya lainnya (Opsional)</label>
                        <div class="relative mt-1">
                          <input type="text" 
                               v-model="anotherCostDisplay" 
                               id="anotherCost" 
                               class="w-full pl-8 pr-2 border rounded-md dark:bg-slate-900 dark:border-slate-600" 
                               @input="handleAnotherCostInput"
                               placeholder="Masukkan biaya lainnya (Opsional)" />
                          <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                              Rp
                          </span>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <div class="input-group">
                            <label for="panjang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Panjang</label>
                            <div class="relative mt-1">
                              <input type="number" 
                                  v-model="panjang" 
                                  id="panjang" 
                                  class="w-full pr-8 pl-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"  
                                  placeholder="Masukkan Panjang" />
                              <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                                  cm
                              </span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="lebar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lebar</label>
                            <div class="relative mt-1">
                              <input type="number" 
                                  v-model="lebar" 
                                  id="lebar" 
                                  class="w-full pr-8 pl-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"  
                                  placeholder="Masukkan Lebar" />
                              <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                                  cm
                              </span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="tinggi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tinggi</label>
                            <div class="relative mt-1">
                              <input type="number" 
                                  v-model="tinggi" 
                                  id="tinggi" 
                                  class="w-full pr-8 pl-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"  
                                  placeholder="Masukkan Tinggi" />
                              <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300 ">
                                  cm
                              </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <div class="input-group">
                            <label for="berat" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Berat Produk</label>
                            <div class="relative mt-1">
                              <input type="number" 
                                  v-model="berat" 
                                  id="berat" 
                                  class="w-full pr-8 pl-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"  
                                  placeholder="Masukkan Berat" />
                              <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                                  gr
                              </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <PrimaryButton @click="calculatePrices">Kalkulasi</PrimaryButton>
                    </div>

                    <!-- <div class="results mt-4">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hasil Kalkukasi</h2><br>
                        <p>Harga Jual Lokal: <br>{{ formatCurrency(localPrice) }}</p><br>
                        <p>Harga Jual Luar: <br>{{ formatCurrency(aboardPrice) }}</p><br>
                        <p>Volume Produk Lokal: <br>{{ volume }} gram</p><br>
                        <p>Volume Produk Luar: <br>{{ aboardVolume }} gram</p><br>
                    </div> -->

                    <hr class="my-4 border-t border-slate-200 dark:border-slate-700" />

                    <div class="space-y-4">
                      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hasil Kalkukasi</h2>
                      <table>
                        <tbody>
                            <tr class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">Harga Jual Lokal</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"><strong> {{formatCurrency(localPrice)}} </strong></td>
                            </tr>
                            <tr class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">Harga Jual Luar</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"><strong> {{formatCurrency(aboardPrice)}} </strong></td>
                            </tr>
                            <tr class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">Volume Lokal</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"><strong> {{volume}} gram </strong></td>
                            </tr>
                            <tr class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">Volume Luar</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"><strong> {{aboardVolume}} gram </strong></td>
                            </tr>
                        </tbody>
                      </table>
                    </div>

                    <hr class="my-4 border-t border-slate-200 dark:border-slate-700" />

                    <div class="results mt-4">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Proses Kalkulasi</h2><br>
                        <pre>Harga Jual Lokal: <br><strong>CEILING((Harga Produksi + Biaya Lainnya) / (1 - 0.33 + 0.1), 10000)</strong></pre><br>
                        <pre>Harga Jual Luar: <br><strong>CEILING(((Harga Produksi + Ongkir to Kurasi + Ongkir to Customer + Biaya Lainnya) * 2.5) - Ongkir to Customer, 10000)</strong></pre><br>
                        <pre>Volume Produk Lokal: <br><strong>((Panjang + 6) * (Lebar + 6) * (Tinggi + 6))/6000</strong></pre><br>
                        <pre>Volume Produk Luar: <br><strong>((Panjang + 6) * (Lebar + 6) * (Tinggi + 6))/5000</strong></pre><br>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
