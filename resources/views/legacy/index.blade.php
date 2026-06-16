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

    <section id="statistik" class="relative pt-16 pb-16 bg-white overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-teal-200 to-transparent"></div>
      <div class="container mx-auto px-4 relative z-10">
        <div class="w-full px-4 mb-10 text-center">
          <span class="inline-block text-xs font-bold uppercase tracking-widest text-amber-500 mb-2">Transparansi Data</span>
          <h2 class="font-bold text-teal-500 text-2xl mb-4 sm:text-3xl lg:text-4xl">Ringkasan Penyaluran</h2>
          <p class="font-medium text-md text-slate-500">Data terkini realisasi program bantuan sosial SI BanTal.</p>
        </div>
        <div class="flex flex-wrap justify-center gap-6">
          <div class="w-full sm:w-1/2 lg:w-1/4 bg-slate-50 rounded-xl shadow-md p-6 text-center border-t-4 border-teal-500 hover:shadow-lg hover:-translate-y-1 transition duration-300">
            <h3 class="font-bold text-3xl text-slate-900 mb-2"><?= number_format($total_kk, 0, ',', '.') ?></h3>
            <p class="text-slate-500 font-medium">Keluarga Penerima (KK)</p>
          </div>
          <div class="w-full sm:w-1/2 lg:w-1/4 bg-slate-50 rounded-xl shadow-md p-6 text-center border-t-4 border-amber-500 hover:shadow-lg hover:-translate-y-1 transition duration-300">
            <h3 class="font-bold text-3xl text-slate-900 mb-2"><?= number_format($total_program_aktif, 0, ',', '.') ?></h3>
            <p class="text-slate-500 font-medium">Program Aktif</p>
          </div>
          <div class="w-full sm:w-1/2 lg:w-1/4 bg-slate-50 rounded-xl shadow-md p-6 text-center border-t-4 border-slate-700 hover:shadow-lg hover:-translate-y-1 transition duration-300">
            <h3 class="font-bold text-3xl text-slate-900 mb-2"><?= number_format($total_desa, 0, ',', '.') ?></h3>
            <p class="text-slate-500 font-medium">Desa Terjangkau</p>
          </div>
        </div>
      </div>
    </section>

@include('layouts.footer')
