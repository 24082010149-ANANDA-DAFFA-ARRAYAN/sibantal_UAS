@include('layouts.header')


    <section id="home" class="relative pt-36 pb-24 bg-slate-50 overflow-hidden">
      <!-- Gradient blobs -->
      <div class="absolute -top-24 -left-24 w-96 h-96 bg-teal-300/30 rounded-full blur-3xl"></div>
      <div class="absolute top-1/3 -right-24 w-[28rem] h-[28rem] bg-amber-300/20 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 left-1/3 w-72 h-72 bg-slate-300/30 rounded-full blur-3xl"></div>

      <!-- Dot pattern -->
      <svg class="absolute inset-0 w-full h-full opacity-[0.15]" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <pattern id="dot-pattern" width="28" height="28" patternUnits="userSpaceOnUse">
            <circle cx="2" cy="2" r="1.4" fill="#0f766e"></circle>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#dot-pattern)"></rect>
      </svg>

      <div class="container mx-auto px-4 relative z-10">
        <div class="flex flex-wrap items-center">
          <div class="w-full self-center px-4 lg:w-1/2">
            <span class="inline-flex items-center gap-2 bg-teal-50 border border-teal-200 text-teal-700 text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full mb-5 shadow-sm">
              <span class="w-2 h-2 bg-teal-500 rounded-full animate-pulse"></span>
              Platform Digital Bantuan Sosial
            </span>
            <h1 class="text-base font-semibold text-teal-500 md:text-xl">
              Selamat Datang di
              <span class="block font-bold text-slate-900 text-4xl mt-1 lg:text-5xl">Dashboard SI BanTal</span>
            </h1>
            <h2 class="font-medium text-slate-500 text-lg mb-5 mt-2 lg:text-2xl">
              Sistem Informasi Bantuan Sosial
            </h2>
            <p class="font-medium text-slate-400 mb-10 leading-relaxed max-w-md">
              Kelola dan pantau program bantuan sosial dengan mudah, transparan,
              dan tepat sasaran melalui layanan digital terpadu.
            </p>

            <div class="flex flex-wrap items-center gap-4">
              <a href="<?= $btn_link ?>" class="text-base font-semibold text-white bg-teal-500 py-3 px-8 rounded-full hover:shadow-xl hover:shadow-teal-500/30 hover:bg-teal-600 hover:-translate-y-0.5 transition duration-300 ease-in-out inline-block">
                <?= $btn_text ?>
              </a>
              <a href="portofolio.php" class="text-base font-semibold text-slate-700 py-3 px-8 rounded-full border border-slate-200 bg-white/70 backdrop-blur-sm hover:bg-white hover:shadow-md transition duration-300 ease-in-out inline-block">
                Lihat Program Bantuan
              </a>
            </div>
          </div>
          <div class="w-full self-end px-4 lg:w-1/2 mt-10 lg:mt-0">
            <div class="relative right-0">
              <div class="absolute -inset-4 bg-gradient-to-tr from-teal-200/40 to-amber-200/40 rounded-3xl blur-xl"></div>
              <img src="bansos.jpg" alt="Bansos" class="relative max-w-full mx-auto rounded-xl shadow-2xl hover:scale-105 transition duration-500 ease-in-out" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="statistik" class="relative pt-16 pb-16 bg-gradient-to-b from-white via-teal-50/30 to-white overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-teal-200 to-transparent"></div>
      <div class="absolute -top-10 right-10 w-64 h-64 bg-teal-200/20 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 left-10 w-72 h-72 bg-amber-200/20 rounded-full blur-3xl"></div>

      <div class="container mx-auto px-4 relative z-10">
        <div class="w-full px-4 mb-10 text-center">
          <span class="inline-block text-xs font-bold uppercase tracking-widest text-amber-500 mb-2">Transparansi Data</span>
          <h2 class="font-bold text-teal-500 text-2xl mb-4 sm:text-3xl lg:text-4xl">Ringkasan Penyaluran</h2>
          <p class="font-medium text-md text-slate-500">Data terkini realisasi program bantuan sosial SI BanTal.</p>
        </div>
        <div class="flex flex-wrap justify-center gap-6">
          <div class="w-full sm:w-1/2 lg:w-1/4 bg-white rounded-xl shadow-md p-6 text-center border-t-4 border-teal-500 hover:shadow-xl hover:-translate-y-1 transition duration-300">
            <div class="w-12 h-12 mx-auto mb-4 bg-teal-50 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h3 class="font-bold text-3xl text-slate-900 mb-2"><?= number_format($total_kk, 0, ',', '.') ?></h3>
            <p class="text-slate-500 font-medium">Keluarga Penerima (KK)</p>
          </div>
          <div class="w-full sm:w-1/2 lg:w-1/4 bg-white rounded-xl shadow-md p-6 text-center border-t-4 border-amber-500 hover:shadow-xl hover:-translate-y-1 transition duration-300">
            <div class="w-12 h-12 mx-auto mb-4 bg-amber-50 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <h3 class="font-bold text-3xl text-slate-900 mb-2"><?= number_format($total_program_aktif, 0, ',', '.') ?></h3>
            <p class="text-slate-500 font-medium">Program Aktif</p>
          </div>
          <div class="w-full sm:w-1/2 lg:w-1/4 bg-white rounded-xl shadow-md p-6 text-center border-t-4 border-slate-700 hover:shadow-xl hover:-translate-y-1 transition duration-300">
            <div class="w-12 h-12 mx-auto mb-4 bg-slate-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h3 class="font-bold text-3xl text-slate-900 mb-2"><?= number_format($total_desa, 0, ',', '.') ?></h3>
            <p class="text-slate-500 font-medium">Desa Terjangkau</p>
          </div>
        </div>
      </div>
    </section>

@include('layouts.footer')
