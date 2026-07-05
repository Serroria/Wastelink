<div id="globalModalOverlay" class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">
    <div id="globalModal" class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-start justify-between">
            <h3 id="globalModalTitle" class="text-lg font-bold text-slate-800">Informasi</h3>
            <button id="globalModalCloseX"
                class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
        </div>

        <div id="globalModalBody" class="mt-3 text-sm text-slate-700">Pesan</div>

        <div class="mt-5 flex justify-end gap-3">
            <button id="globalModalCancel"
                class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200">Tutup</button>
            <button id="globalModalConfirm"
                class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">OK</button>
        </div>
    </div>
</div>