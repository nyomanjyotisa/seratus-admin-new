<script setup>
import { ref, onMounted } from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";

function formatNumber(value) {
  return new Intl.NumberFormat("id-ID").format(value);
}

function removeSeparators(value) {
  return parseInt(value.replace(/\D/g, ""), 10) || 0;
}

// Load the value from localStorage or default to 50000000
const maksimalSaldoKas = ref(parseInt(localStorage.getItem("maksimalSaldoKas")) || 50000000);
const isEditing = ref(false);
const editedSaldo = ref(formatNumber(maksimalSaldoKas.value));
const errorMessage = ref("");

// Load stored value when the component mounts
onMounted(() => {
  const savedValue = localStorage.getItem("maksimalSaldoKas");
  if (savedValue !== null) {
    maksimalSaldoKas.value = parseInt(savedValue);
  }
});

const startEditing = () => {
  isEditing.value = true;
  editedSaldo.value = formatNumber(maksimalSaldoKas.value);
  errorMessage.value = "";
};

const handleInput = (event) => {
  const rawValue = removeSeparators(event.target.value);
  editedSaldo.value = formatNumber(rawValue);
};

const saveSaldoKas = () => {
  const newValue = removeSeparators(editedSaldo.value);
  if (newValue <= 0) {
    errorMessage.value = "Maksimal saldo kas tidak boleh 0 atau kosong.";
    return;
  }
  maksimalSaldoKas.value = newValue;
  localStorage.setItem("maksimalSaldoKas", newValue); // Save to localStorage
  isEditing.value = false;
};

const cancelEditing = () => {
  isEditing.value = false;
  errorMessage.value = "";
};
</script>

<template>
  <Head title="Pengaturan" />
  <AuthenticatedLayout>
    <Breadcrumb :title="'Pengaturan'" :breadcrumbs="[]" />
    <div class="space-y-4">
      <div class="relative bg-white dark:bg-slate-800 shadow sm:rounded-lg p-6">
        <div class="flex flex-col space-y-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Maksimal Saldo Kas
          </label>
          
          <div v-if="!isEditing" class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-md">
            <span class="text-lg font-semibold text-gray-900 dark:text-gray-200">
              Rp {{ maksimalSaldoKas.toLocaleString("id-ID") }}
            </span>
            <PrimaryButton @click="startEditing">Edit</PrimaryButton>
          </div>

          <div v-else class="space-y-2">
            <input
              type="text"
              v-model="editedSaldo"
              @input="handleInput"
              class="w-full px-3 py-2 border rounded-md dark:bg-slate-900 dark:border-slate-600"
              placeholder="Masukkan maksimal saldo kas"
            />
            <p v-if="errorMessage" class="text-red-500 text-sm">{{ errorMessage }}</p>

            <div class="flex space-x-2">
              <PrimaryButton @click="saveSaldoKas">Save</PrimaryButton>
              <button @click="cancelEditing" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md">
                Cancel
              </button>
            </div>
          </div>

          <p class="text-sm text-gray-700 dark:text-gray-300">
            <strong>Note:</strong> Tentukan batas maksimal saldo kas. Jika saldo kas mencapai batas ini saat proses bagi hasil, kelebihan saldo akan dialokasikan ke bagi hasil agar saldo kas tidak melebihi batas yang ditetapkan.
          </p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
