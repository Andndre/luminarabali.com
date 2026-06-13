@props([
    'slug' => 'demo'
])

<section class="invitation-rsvp py-32 relative overflow-hidden bg-gray-50" 
    x-data="{ 
    isSubmitting: false, 
    message: '',
    submitRsvp(e) {
        this.isSubmitting = true;
        let formData = new FormData(e.target);
        
        fetch('{{ route("invitation.rsvp", $slug) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            this.isSubmitting = false;
            this.message = 'Terima kasih atas konfirmasi Anda. Kami menantikan kehadiran Anda!';
            e.target.reset();
        })
        .catch(err => {
            this.isSubmitting = false;
            this.message = 'Terjadi kesalahan saat mengirim RSVP. Silakan coba lagi.';
        });
    }
}">
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-gray-400 to-transparent opacity-30"></div>
    <div class="absolute top-10 left-10 w-32 h-32 border-t border-l border-gray-400 opacity-20"></div>
    <div class="absolute bottom-10 right-10 w-32 h-32 border-b border-r border-gray-400 opacity-20"></div>

    <div class="max-w-2xl mx-auto px-6 text-center reveal-on-scroll relative z-10">
        <h2 class="invitation-title text-5xl md:text-7xl font-serif mb-6 text-gray-900">RSVP</h2>
        <p class="mb-12 font-light text-lg text-gray-600">
            Kindly respond before <br><span class="invitation-accent font-medium text-gray-800">December 1st, 2026</span>
        </p>
        
        <!-- Feedback Message -->
        <div x-show="message" x-transition class="mb-8 p-6 rounded-lg font-serif text-xl border bg-white border-gray-300 text-gray-900">
            <p x-text="message"></p>
        </div>

        <form @submit.prevent="submitRsvp" class="space-y-6 text-left" x-show="!message">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="invitation-accent block text-xs uppercase tracking-widest mb-2 text-gray-600">Nama Lengkap</label>
                    <input type="text" name="guest_name" required 
                           class="w-full bg-transparent border-b border-gray-400 px-0 py-3 focus:outline-none transition-colors text-gray-900 placeholder-gray-400 focus:border-gray-800" 
                           placeholder="John Doe">
                </div>
                <div>
                    <label class="invitation-accent block text-xs uppercase tracking-widest mb-2 text-gray-600">Jumlah Tamu</label>
                    <select name="number_of_guests" required 
                            class="w-full bg-transparent border-b border-gray-400 px-0 py-3 focus:outline-none transition-colors text-gray-900 focus:border-gray-800 [&>option]:bg-white">
                        <option value="1">1 Orang</option>
                        <option value="2">2 Orang</option>
                        <option value="3">3 Orang</option>
                        <option value="4">4 Orang</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="invitation-accent block text-xs uppercase tracking-widest mb-2 mt-4 text-gray-600">Kehadiran</label>
                <select name="attendance_status" required 
                        class="w-full bg-transparent border-b border-gray-400 px-0 py-3 focus:outline-none transition-colors text-gray-900 focus:border-gray-800 [&>option]:bg-white">
                    <option value="">Pilih Status Kehadiran...</option>
                    <option value="hadir">Joyfully Accepts</option>
                    <option value="tidak_hadir">Regretfully Declines</option>
                </select>
            </div>

            <div>
                <label class="invitation-accent block text-xs uppercase tracking-widest mb-2 mt-4 text-gray-600">Pesan / Ucapan</label>
                <textarea name="message" rows="3" 
                          class="w-full bg-transparent border-b border-gray-400 px-0 py-3 focus:outline-none transition-colors text-gray-900 placeholder-gray-400 focus:border-gray-800" 
                          placeholder="Leave a message for the couple..."></textarea>
            </div>
            
            <div class="pt-8 text-center">
                <button type="submit" :disabled="isSubmitting" 
                        class="invitation-button group relative px-12 py-4 uppercase tracking-[0.2em] text-sm font-bold disabled:opacity-50 transition-all duration-500 w-full md:w-auto bg-gray-900 text-white hover:bg-opacity-90 border border-transparent">
                    <span x-show="!isSubmitting">Kirim Konfirmasi</span>
                    <span x-show="isSubmitting">Mengirim...</span>
                </button>
            </div>
        </form>
    </div>
</section>
