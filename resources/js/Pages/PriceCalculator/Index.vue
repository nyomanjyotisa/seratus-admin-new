<script setup>
import { ref, computed, watchEffect, onMounted } from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import ConfirmationModal from "@/Components/ConfirmationModal.vue";

const localPrice = ref(0);
const aboardPrice = ref(0);

const panjang = ref('');
const lebar = ref('');
const tinggi = ref('');
const extraPanjang = ref(6);
const extraLebar = ref(6);
const extraTinggi = ref(6);
const berat = ref(null);
const showModal = ref(false);
const productionCostDisplay = ref("");
const shippingToKurasiDisplay = ref('');
const shippingToCustomerDisplay = ref('');
const anotherCostDisplay = ref('');
const prodInput = ref(null);

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

const formatDimension = (value) => {
  value = value.replace(/[^0-9.]/g, '');
  
  const parts = value.split('.');
  if (parts.length > 2) {
    value = parts[0] + '.' + parts.slice(1).join('');
  }
  
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

  return parts.join('.');
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

const calculateVolume = (length, width, height, divisor, weight, extraLength, extraWidth, extraHeight) => {
  const parsedLength = parseNumber(length);
  const parsedWidth = parseNumber(width);
  const parsedHeight = parseNumber(height);
  const parsedWeight = parseNumber(weight);
  const parsedExtraLength = parseNumber(extraLength);
  const parsedExtraWidth = parseNumber(extraWidth);
  const parsedExtraHeight = parseNumber(extraHeight);

  const calculatedVolume = (((parsedLength + parsedExtraLength) * (parsedWidth + parsedExtraWidth) * (parsedHeight + parsedExtraHeight)) / divisor)*1000;
  if (parsedLength <= 0 || parsedWidth <= 0 || parsedHeight <= 0) {
    return 0;
  }

  if (parsedWeight > calculatedVolume) {
    return parsedWeight;
  }
  return calculatedVolume.toFixed(0);
};

const volume = computed(() => {
  return calculateVolume(panjang.value, lebar.value, tinggi.value, 6000, berat.value, extraPanjang.value, extraLebar.value, extraTinggi.value);
});

const aboardVolume = computed(() => {
  return calculateVolume(panjang.value, lebar.value, tinggi.value, 5000, berat.value, extraPanjang.value, extraLebar.value, extraTinggi.value);
});

const checkWeight = () => {
    if (berat.value < 50 && berat.value != null) {
        showModal.value = true;
    }
};

const resetWeight = () => {
    berat.value = null;
    showModal.value = false;
};

const handleInput = (field) => (event) => {
  let input = event.target.value.replace(/[^\d,\.]/g, '');
  form.value[field] = unformatNumber(input);
};

const handleProdInput = handleInput('productionCost');
const handleShipKurasiInput = handleInput('shippingToKurasi');
const handleShiptoCustomerInput = handleInput('shippingToCustomer');
const handleAnotherCostInput = handleInput('anotherCost');

const updateBerat = (event) => {
  const formattedValue = formatDimension(event.target.value);
  berat.value = formattedValue;
};

const updatePanjang = (event) => {
  const formattedValue = formatDimension(event.target.value);
  panjang.value = formattedValue;
};

const updateLebar = (event) => {
  const formattedValue = formatDimension(event.target.value);
  lebar.value = formattedValue;
};

const updateTinggi = (event) => {
  const formattedValue = formatDimension(event.target.value);
  tinggi.value = formattedValue;
};

const updateExtraPanjang = (event) => {
  const formattedValue = formatDimension(event.target.value);
  extraPanjang.value = formattedValue;
};

const updateExtraLebar = (event) => {
  const formattedValue = formatDimension(event.target.value);
  extraLebar.value = formattedValue;
};

const updateExtraTinggi = (event) => {
  const formattedValue = formatDimension(event.target.value);
  extraTinggi.value = formattedValue;
};

onMounted(() => {
  if (prodInput.value) {
    prodInput.value.focus();
  }
});

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
                                  ref="prodInput"
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
                            <label for="berat" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Berat Aktual Produk</label>
                            <div class="relative mt-1">
                              <input type="text" 
                                  v-model="berat" 
                                  id="berat" 
                                  class="w-full pr-8 pl-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"  
                                  @input="updateBerat"
                                  @blur="checkWeight"
                                  placeholder="Masukkan Berat" />
                              <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                                  gr
                              </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <div class="input-group">
                            <label for="panjang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Panjang Aktual</label>
                            <div class="relative mt-1">
                              <input type="text" 
                                  v-model="panjang" 
                                  id="panjang" 
                                  class="w-full pr-8 pl-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"  
                                  @input="updatePanjang"
                                  placeholder="Masukkan Panjang" />
                              <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                                  cm
                              </span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="lebar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lebar Aktual</label>
                            <div class="relative mt-1">
                              <input type="text" 
                                  v-model="lebar" 
                                  id="lebar" 
                                  class="w-full pr-8 pl-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"  
                                  @input="updateLebar"
                                  placeholder="Masukkan Lebar" />
                              <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                                  cm
                              </span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="tinggi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tinggi Aktual</label>
                            <div class="relative mt-1">
                              <input type="text" 
                                  v-model="tinggi" 
                                  id="tinggi" 
                                  class="w-full pr-8 pl-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"  
                                  @input="updateTinggi"
                                  placeholder="Masukkan Tinggi" />
                              <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300 ">
                                  cm
                              </span>
                            </div>
                        </div>
                    </div>
                    <p class="block text-sm font-medium text-gray-700 dark:text-gray-100">
                      <strong class="font-bold">Note:</strong>
                    </p>
                    <p class="block text-sm font-medium text-gray-700 dark:text-gray-100">
                      <strong class="font-bold">• Ganti value tambahan PxLxT sesuai dengan tambahan dimensi setelah packing (Jika perlu)</strong>
                    </p>
                    <p class="block text-sm font-medium text-gray-700 dark:text-gray-100">
                      <strong class="font-bold">• Default value untuk tambahan PxLxT adalah 6 cm, dengan asumsi dimensi barang bertambah 6 cm setelah packing</strong>
                    </p>

                    <div class="flex space-x-4">
                        <div class="input-group">
                            <label for="extraPanjang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tambahan Panjang</label>
                            <div class="relative mt-1">
                              <input type="text" 
                                  v-model="extraPanjang" 
                                  id="extraPanjang" 
                                  class="w-full pr-8 pl-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"  
                                  @input="updateExtraPanjang"
                                  placeholder="Tambahan Panjang" />
                              <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                                  cm
                              </span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="extraLebar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tambahan Lebar</label>
                            <div class="relative mt-1">
                              <input type="text" 
                                  v-model="extraLebar" 
                                  id="extraLebar" 
                                  class="w-full pr-8 pl-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"  
                                  @input="updateExtraLebar"
                                  placeholder="Tambahan Lebar" />
                              <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300">
                                  cm
                              </span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="extraTinggi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tambahan Tinggi</label>
                            <div class="relative mt-1">
                              <input type="text" 
                                  v-model="extraTinggi" 
                                  id="extraTinggi" 
                                  class="w-full pr-8 pl-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"  
                                  @input="updateExtraTinggi"
                                  placeholder="Tambahan Tinggi" />
                              <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-700 dark:text-gray-300 ">
                                  cm
                              </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <PrimaryButton @click="calculatePrices">Kalkulasi</PrimaryButton>
                    </div>

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
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"><strong> {{new Intl.NumberFormat().format(volume)}} gram </strong></td>
                            </tr>
                            <tr class="border-t border-slate-200 dark:border-slate-700 hover:bg-slate-200/30 hover:dark:bg-slate-900/20">
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3">Volume Luar</td>
                                <td class="whitespace-nowrap py-4 px-2 sm:py-3"><strong> {{new Intl.NumberFormat().format(aboardVolume)}} gram </strong></td>
                            </tr>
                        </tbody>
                      </table>
                    </div>

                    <hr class="my-4 border-t border-slate-200 dark:border-slate-700" />

                    <div class="results mt-4">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Proses Kalkulasi</h2><br>
                        <pre>Harga Jual Lokal: <br><strong>CEILING((Harga Produksi + Biaya Lainnya) / (1 - 0.33 + 0.1), 10000)</strong></pre><br>
                        <pre>Harga Jual Luar: <br><strong>CEILING(((Harga Produksi + Ongkir to Kurasi + Ongkir to Customer + Biaya Lainnya) * 2.5) - Ongkir to Customer, 10000)</strong></pre><br>
                        <pre>Volume Produk Lokal: <br><strong>((Panjang Aktual + Panjang Tambahan) * (Lebar Aktual + Lebar Tambahan) * (Tinggi Aktual + Tinggi Tambahan))/6000</strong></pre><br>
                        <pre>Volume Produk Luar: <br><strong>((Panjang Aktual + Panjang Tambahan) * (Lebar Aktual + Lebar Tambahan) * (Tinggi Aktual + Tinggi Tambahan))/5000</strong></pre><br>
                    </div>
                </div>
            </div>
        </div>
        <ConfirmationModal
          v-if="showModal"
          v-model:show="showModal"
          title="Berat Produk Tidak Valid"
          message="Harap masukkan berat dalam Gram, jangan Kilogram. 1 Kilogram = 1000 Gram."
          okText="OK"
          :showCancel="false"
          @confirm="resetWeight"
        />
    </AuthenticatedLayout>
</template>
