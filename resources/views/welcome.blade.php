<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ORDON | Organ Donation and Transplant Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#eef7fc] text-slate-900 font-sans antialiased scroll-smooth">
    <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-[#d7e8f4]" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('images/ordon-logo.png') }}" class="h-12 w-auto rounded-xl" alt="ORDON logo">
                <div>
                    <p class="font-semibold tracking-wide text-[#0b3650]">ORDON</p>
                    <p class="text-xs text-slate-500">Organ Donation and Transplant Platform</p>
                </div>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm text-slate-600">
                <a href="#about" class="hover:text-[#0b6ea2] transition-all duration-200">About Us</a>
                <a href="#stories" class="hover:text-[#0b6ea2] transition-all duration-200">Success Stories</a>
                <a href="#faq" class="hover:text-[#0b6ea2] transition-all duration-200">FAQ</a>
                <a href="{{ route('contact') }}" class="hover:text-[#0b6ea2] transition-all duration-200">Contact Us</a>
            </nav>
            <div class="flex items-center gap-3">
<!--                 <button @click="mobileMenu = !mobileMenu" class="md:hidden rounded-xl border border-[#9ac8e4] px-3 py-2 text-sm text-[#0b3650]">
                    Menu
                </button>  -->
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl bg-[#0b6ea2] text-white font-medium transition-all duration-200 hover:bg-[#0a5f8b]">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline-block">
                        @csrf
                        <button class="px-4 py-2 rounded-xl border border-rose-200 text-rose-700 hover:bg-rose-50 transition-all duration-200">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl border border-[#9ac8e4] hover:bg-[#e9f4fb] transition-all duration-200">Login</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-[#0b6ea2] text-white font-semibold hover:bg-[#0a5f8b] transition-all duration-200">Register</a>
                @endauth
            </div>
        </div>
        <div x-show="mobileMenu" x-transition class="md:hidden border-t border-[#d7e8f4] bg-white px-6 py-3">
            <nav class="flex flex-col gap-2 text-sm text-slate-700">
                <a @click="mobileMenu=false" href="#about" class="rounded-xl px-2 py-2 hover:bg-[#eef7fc]">About Us</a>
                <a @click="mobileMenu=false" href="#stories" class="rounded-xl px-2 py-2 hover:bg-[#eef7fc]">Success Stories</a>
                <a @click="mobileMenu=false" href="#faq" class="rounded-xl px-2 py-2 hover:bg-[#eef7fc]">FAQ</a>
                <a @click="mobileMenu=false" href="{{ route('contact') }}" class="rounded-xl px-2 py-2 hover:bg-[#eef7fc]">Contact Us</a>
            </nav>
        </div>
    </header>

    <section class="max-w-7xl mx-auto px-6 pt-16 pb-12 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center fade-in-up">
        <div>
            <p class="inline-flex px-3 py-1 rounded-full text-xs bg-[#d9eefb] text-[#0b6ea2] mb-4">Trusted digital organ donation ecosystem</p>
            <h1 class="text-4xl lg:text-6xl font-semibold leading-tight text-[#0b3650]">Helping patients receive life-saving organ transplants faster.</h1>
            <p class="text-slate-600 mt-6 text-lg leading-relaxed">
                ORDON brings together donors, recipients, and hospitals with transparent workflows and intelligent matching
                to support fair and timely transplant decisions.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row flex-wrap gap-3">
                <a href="{{ route('register') }}" class="text-center px-6 py-3 rounded-2xl bg-[#0b6ea2] text-white font-semibold hover:bg-[#0a5f8b] transition-all duration-200">Become a Donor</a>
                <a href="{{ route('register') }}" class="text-center px-6 py-3 rounded-2xl border border-[#9ac8e4] hover:bg-[#e9f4fb] transition-all duration-200">Need a Transplant</a>
            </div>
        </div>
        <div class="card-pro">
            <img src="{{ asset('images/ordon-landing-hero.png') }}" alt="ORDON platform hero" class="w-full max-w-md mx-auto rounded-2xl" />
            <div class="mt-6 border-t border-[#d7e8f4] pt-5 fade-in-up">
                <h3 class="text-xl font-semibold text-[#0b3650] text-center">Why Organ Donation Matters</h3>
                <p class="text-sm text-slate-600 text-center mt-2 leading-relaxed">
                    A single donor can save multiple lives through timely and safe transplantation. Early registration, family awareness,
                    and informed consent can reduce waiting time for patients in critical need.
                </p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <span class="rounded-full bg-[#e8f4fb] text-[#0b6ea2] text-xs px-3 py-1">One Donor Can Save Lives</span>
                    <span class="rounded-full bg-[#e8f4fb] text-[#0b6ea2] text-xs px-3 py-1">Family Consent is Important</span>
                    <span class="rounded-full bg-[#e8f4fb] text-[#0b6ea2] text-xs px-3 py-1">Donate with Medical Guidance</span>
                </div>
                <div class="grid grid-cols-3 gap-3 mt-5 text-center">
                    <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Potential Lives Helped</p>
                        <p class="text-lg font-semibold text-[#0b3650]">Up to 8</p>
                    </div>
                    <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Patients Waiting</p>
                        <p class="text-lg font-semibold text-[#0b3650]">Many</p>
                    </div>
                    <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Best Time to Register</p>
                        <p class="text-lg font-semibold text-[#0b3650]">Today</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="max-w-7xl mx-auto px-6 py-12 fade-in-up">
        <div class="card-pro">
            <h2 class="text-3xl font-semibold text-[#0b3650]">About Us</h2>
            <p class="text-slate-600 mt-4 leading-relaxed">
                ORDON is built to support a safer and more compassionate organ donation journey by connecting donors, recipients,
                hospitals, and care teams through one transparent ecosystem. We focus on reducing delays in life-saving decisions
                while maintaining ethical standards, medical responsibility, and trust for families.
            </p>
            <p class="text-slate-600 mt-3 leading-relaxed">
                Our approach highlights responsible donor registration, secure identity verification, hospital-led clinical review,
                and continuous status visibility so every stakeholder understands what happens next in the transplant process.
                This helps improve communication, reduce confusion, and strengthen confidence during critical medical situations.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-4">
                    <h3 class="font-semibold text-[#0b3650]">Our Mission</h3>
                    <p class="text-sm text-slate-600 mt-2">Promote timely, fair, and medically guided organ donation to save more lives.</p>
                </div>
                <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-4">
                    <h3 class="font-semibold text-[#0b3650]">Our Values</h3>
                    <p class="text-sm text-slate-600 mt-2">Transparency, patient safety, dignity of donors, and accountability in approvals.</p>
                </div>
                <div class="rounded-xl border border-[#d7e8f4] bg-slate-50 p-4">
                    <h3 class="font-semibold text-[#0b3650]">Our Commitment</h3>
                    <p class="text-sm text-slate-600 mt-2">Support families with clear updates and ensure each case follows verified workflows.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="stories" class="max-w-7xl mx-auto px-6 py-12 fade-in-up" x-data="{ index: 0, slides: [
        { title: 'Kidney Match Completed', text: 'A donor and recipient were matched with high urgency scoring and approved within one day.' },
        { title: 'Hospital-led Coordination', text: 'Hospital teams used ORDON approvals and transplant scheduling to reduce communication delays.' },
        { title: 'Transparent Family Updates', text: 'Status visibility gave families confidence from registration through transplant completion.' }
    ] }" x-init="setInterval(() => index = (index + 1) % slides.length, 5000)">
        <h2 class="text-3xl font-semibold mb-6 text-[#0b3650]">Success Stories</h2>
        <div class="card-pro min-h-[200px]">
            <p class="text-[#0b6ea2] text-sm mb-2" x-text="'Story ' + (index + 1)"></p>
            <h3 class="text-2xl font-semibold text-[#0b3650]" x-text="slides[index].title"></h3>
            <p class="text-slate-600 mt-4" x-text="slides[index].text"></p>
            <div class="flex gap-2 mt-6">
                <template x-for="(slide, i) in slides" :key="i">
                    <button @click="index = i" class="h-2.5 rounded-full transition-all duration-200"
                        :class="index === i ? 'w-8 bg-[#0b6ea2]' : 'w-2.5 bg-[#bedcf0]'"></button>
                </template>
            </div>
        </div>
    </section>

    <section id="faq" class="max-w-7xl mx-auto px-6 py-12 fade-in-up">
        <h2 class="text-3xl font-semibold mb-6 text-[#0b3650]">Frequently Asked Questions</h2>
        <div class="space-y-3" x-data="{ open: 1 }">
            <div class="card-pro">
                <button @click="open = open === 1 ? 0 : 1" class="w-full text-left flex justify-between items-center">
                    <span class="font-medium text-[#0b3650]">Who can register on ORDON?</span>
                    <span x-text="open === 1 ? '-' : '+'"></span>
                </button>
                <p x-show="open === 1" x-transition class="text-slate-600 mt-3">Anyone can register as a donor, recipient, or hospital. Admin accounts are managed internally.</p>
            </div>
            <div class="card-pro">
                <button @click="open = open === 2 ? 0 : 2" class="w-full text-left flex justify-between items-center">
                    <span class="font-medium text-[#0b3650]">How are organ matches prioritized?</span>
                    <span x-text="open === 2 ? '-' : '+'"></span>
                </button>
                <p x-show="open === 2" x-transition class="text-slate-600 mt-3">ORDON uses blood compatibility, urgency level, and waiting time to rank recipients fairly.</p>
            </div>
            <div class="card-pro">
                <button @click="open = open === 3 ? 0 : 3" class="w-full text-left flex justify-between items-center">
                    <span class="font-medium text-[#0b3650]">Can recipients track their transplant progress?</span>
                    <span x-text="open === 3 ? '-' : '+'"></span>
                </button>
                <p x-show="open === 3" x-transition class="text-slate-600 mt-3">Yes. Every recipient can monitor request status from REGISTERED to COMPLETED inside their dashboard.</p>
            </div>
            <div class="card-pro">
                <button @click="open = open === 4 ? 0 : 4" class="w-full text-left flex justify-between items-center">
                    <span class="font-medium text-[#0b3650]">Are hospital approvals required before transplant?</span>
                    <span x-text="open === 4 ? '-' : '+'"></span>
                </button>
                <p x-show="open === 4" x-transition class="text-slate-600 mt-3">Yes. Hospitals must review and approve matches before transplant scheduling and completion.</p>
            </div>
        </div>
    </section>

    <footer class="mt-12 border-t border-[#d7e8f4] bg-white">
        <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <img src="{{ asset('images/ordon-logo.png') }}" class="h-12 w-auto rounded-xl" alt="ORDON logo">
                <p class="text-slate-500 mt-4 text-sm">ORDON - Organ Donation and Transplant Platform.</p>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-[#0b3650]">Quick Links</h4>
                <ul class="space-y-2 text-slate-500 text-sm">
                    <li><a href="#about" class="hover:text-[#0b6ea2]">About Us</a></li>
                    <li><a href="#stories" class="hover:text-[#0b6ea2]">Success Stories</a></li>
                    <li><a href="#faq" class="hover:text-[#0b6ea2]">FAQ</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-[#0b6ea2]">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-3 text-[#0b3650]">Get in Touch</h4>
                <p class="text-slate-500 text-sm">support@ordon.org</p>
                <p class="text-slate-500 text-sm mt-1">+880 1234 567890</p>
                <p class="text-slate-500 text-sm mt-1">Dhaka, Bangladesh</p>
            </div>
        </div>
        <div class="border-t border-[#d7e8f4] py-4 text-center text-xs text-slate-500">
            © {{ date('Y') }} ORDON. All rights reserved.
        </div>
    </footer>

    <div class="fixed bottom-5 right-5 z-40" x-data="{
        open: false,
        text: '',
        typing: false,
        messages: [
            { from: 'bot', text: 'Hello! I am ORDON assistant. Ask about donation, recipient matching, or hospital approvals.', time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }
        ],
        makeReply(value) {
            if (value.includes('donor')) return 'You can register as a donor, complete your profile, and keep availability ON for matching.';
            if (value.includes('recipient')) return 'Recipients should update urgency, waiting time, and verify profile to improve matching priority.';
            if (value.includes('hospital')) return 'Hospitals review MATCHED cases, approve/reject requests, and mark transplants completed.';
            if (value.includes('contact')) return 'Use the Contact Us page for support: email support@ordon.org.';
            return 'Thanks for your message. ORDON helps with fair organ matching and transplant coordination.';
        },
        send() {
            if (!this.text.trim()) return;
            const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            this.messages.push({ from: 'user', text: this.text, time: now });
            const value = this.text.toLowerCase();
            this.text = '';
            this.typing = true;
            setTimeout(() => {
                this.messages.push({ from: 'bot', text: this.makeReply(value), time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) });
                this.typing = false;
            }, 700);
        }
    }">
        <button @click="open = !open" class="rounded-full h-14 w-14 bg-[#0b6ea2] text-white shadow-lg hover:bg-[#0a5f8b] transition-all duration-200">💬</button>
        <div x-show="open" x-transition class="mt-3 w-80 card-pro">
            <p class="font-semibold text-[#0b3650] mb-2">ORDON Quick Assistant</p>
            <p class="text-xs text-slate-500 mb-2">Ask me anything about donor registration, matching priority, hospital approvals, or support.</p>
            <div class="rounded-xl bg-slate-50 p-3 min-h-[170px] max-h-[240px] overflow-y-auto space-y-2">
                <template x-for="(message, idx) in messages" :key="idx">
                    <div :class="message.from === 'user' ? 'text-right' : 'text-left'">
                        <p :class="message.from === 'user' ? 'inline-block bg-[#0b6ea2] text-white' : 'inline-block bg-white text-slate-700'" class="text-sm rounded-xl px-3 py-2 border border-[#d7e8f4]" x-text="message.text"></p>
                        <p class="text-[10px] text-slate-400 mt-1" x-text="message.time"></p>
                    </div>
                </template>
                <p x-show="typing" class="text-xs text-slate-500 italic">Assistant is typing...</p>
            </div>
            <div class="mt-3 flex gap-2">
                <input x-model="text" @keydown.enter.prevent="send()" class="flex-1 rounded-xl border-[#c8dfef]" placeholder="Ask me anything...">
                <button @click="send()" class="px-4 rounded-xl bg-[#0b6ea2] text-white">Send</button>
            </div>
        </div>
    </div>

    <div
        class="fixed bottom-24 right-5 z-40"
        x-data="{ show: false }"
        x-init="window.addEventListener('scroll', () => show = window.scrollY > 260)"
    >
        <button
            x-show="show"
            x-transition
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-[#0b6ea2] to-[#0a5f8b] text-white shadow-lg hover:shadow-xl px-4 py-2.5 transition-all duration-200 border border-[#7ec1e6]/40"
            aria-label="Back to top"
        >
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/20 text-sm">↑</span>
            <span class="text-xs font-semibold tracking-wide">Back to Top</span>
        </button>
    </div>
</body>
</html>
