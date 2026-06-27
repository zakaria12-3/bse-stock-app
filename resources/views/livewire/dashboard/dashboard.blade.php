<div>
    <div class="space-y-6">
        <!-- Filter Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-card p-4 rounded-lg border border-border shadow-sm">
        <div>
            <h2 class="text-lg font-semibold text-foreground">Vue atelier</h2>
            <p class="text-sm text-muted-foreground">Suivi des dossiers ambulance, du stock et de la finance BSE.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Period Selector -->
            <select wire:model.live="dateFilter" class="h-9 w-[180px] rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                @foreach(\App\Enums\DatePeriod::cases() as $period)
                    <option value="{{ $period->value }}">{{ $period->label() }}</option>
                @endforeach
            </select>

            <!-- Custom Date Range -->
            <!-- Custom Date Range (Flatpickr) -->
            <div x-show="$wire.dateFilter === 'custom'" x-transition class="flex items-center gap-2"
                 x-data="{
                     init() {
                         flatpickr(this.$refs.picker, {
                             mode: 'range',
                             dateFormat: 'Y-m-d',
                             defaultDate: [this.$wire.customStartDate, this.$wire.customEndDate],
                             onChange: (selectedDates, dateStr, instance) => {
                                 if (selectedDates.length === 2) {
                                     this.$wire.updateCustomRange(
                                         instance.formatDate(selectedDates[0], 'Y-m-d'),
                                         instance.formatDate(selectedDates[1], 'Y-m-d')
                                     );
                                 }
                             }
                         });
                     }
                 }"
            >
                <input x-ref="picker" type="text" class="h-9 w-[240px] rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="Choisir une periode...">
            </div>

             <!-- Refresh Button -->
             <button wire:click="$refresh" class="print:hidden inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 w-9">
                <x-heroicon-o-arrow-path wire:loading.class="animate-spin" class="h-4 w-4" />
            </button>
            
            <!-- Print Button -->
            <button onclick="window.print()" class="print:hidden inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2 gap-2">
                <x-heroicon-o-printer class="h-4 w-4" />
                <span class="hidden sm:inline">Imprimer le rapport</span>
            </button>

            <a href="https://bse-industrie.com/" target="_blank" rel="noopener noreferrer" class="print:hidden inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2 gap-2">
                <x-heroicon-o-globe-alt class="h-4 w-4" />
                <span class="hidden sm:inline">Site BSE</span>
            </a>
        </div>
    </div>

    <!-- BSE Website Connector -->
    <div class="grid gap-4 lg:grid-cols-[1.2fr_2fr] print:hidden">
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 flex flex-col gap-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold leading-none tracking-tight">Site public BSE</h3>
                        <p class="mt-1 text-xs text-muted-foreground">Acces direct au site vitrine, au catalogue, au devis et aux offres ambulance.</p>
                    </div>
                    <x-heroicon-o-globe-alt class="h-5 w-5 text-muted-foreground" />
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="https://bse-industrie.com/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-md bg-primary px-3 py-2 text-xs font-semibold text-primary-foreground hover:bg-primary/90">
                        Ouvrir le site
                    </a>
                    <a href="https://bse-industrie.com/contact/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-md border border-input bg-background px-3 py-2 text-xs font-semibold hover:bg-accent">
                        Contact / devis
                    </a>
                </div>
                <div class="rounded-md bg-muted/50 p-3 text-xs text-muted-foreground">
                    BSE Industrie, Route de Souss GP1 km 5,5, 2033 Megrine. Tel : +216 79 297 450.
                </div>
            </div>
        </div>

        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 flex flex-col gap-3">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-semibold leading-none tracking-tight">Liens utiles pour les dossiers atelier</h3>
                        <p class="mt-1 text-xs text-muted-foreground">Raccourcis vers les pages publiques a consulter lors d'un devis, d'une renovation ou d'un dossier SAV.</p>
                    </div>
                    <a href="https://bse-industrie.com/catalogue-ambulances/" target="_blank" rel="noopener noreferrer" class="hidden sm:inline-flex items-center gap-2 rounded-md border border-input bg-background px-3 py-2 text-xs font-semibold hover:bg-accent">
                        <x-heroicon-o-document-text class="h-4 w-4" />
                        Catalogue
                    </a>
                </div>
                <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-4">
                    <a href="https://bse-industrie.com/ambulances-neuves/" target="_blank" rel="noopener noreferrer" class="rounded-md border border-border p-3 hover:bg-accent">
                        <div class="flex items-center gap-2 text-sm font-semibold">
                            <x-heroicon-o-truck class="h-4 w-4 text-muted-foreground" />
                            Ambulances neuves
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">Categories A/B, 4x4 et configurations EN1789.</p>
                    </a>
                    <a href="https://bse-industrie.com/renovation-ambulances-anciennes-occasions/" target="_blank" rel="noopener noreferrer" class="rounded-md border border-border p-3 hover:bg-accent">
                        <div class="flex items-center gap-2 text-sm font-semibold">
                            <x-heroicon-o-wrench-screwdriver class="h-4 w-4 text-muted-foreground" />
                            Renovation
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">Remplacement et remise a niveau des cellules sanitaires.</p>
                    </a>
                    <a href="https://bse-industrie.com/innovations-ambulances/" target="_blank" rel="noopener noreferrer" class="rounded-md border border-border p-3 hover:bg-accent">
                        <div class="flex items-center gap-2 text-sm font-semibold">
                            <x-heroicon-o-sparkles class="h-4 w-4 text-muted-foreground" />
                            Innovations
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">BSEBact, nettoyage rapide et amenagements pratiques.</p>
                    </a>
                    <a href="https://bse-industrie.com/mbulances-occasion-tunisie/" target="_blank" rel="noopener noreferrer" class="rounded-md border border-border p-3 hover:bg-accent">
                        <div class="flex items-center gap-2 text-sm font-semibold">
                            <x-heroicon-o-clipboard-document-check class="h-4 w-4 text-muted-foreground" />
                            Occasions
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">Vehicules d'occasion et opportunites client.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Sales -->
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Dossiers d'amenagement</h3>
                <x-heroicon-o-truck class="h-4 w-4 text-muted-foreground" />
            </div>
            <div class="p-4 pt-0">
                <div class="text-xl sm:text-2xl font-bold">
                    @money($stats['total_sales'] ?? 0)
                </div>
                <p class="text-xs text-muted-foreground mt-1">
                    {{ $stats['sales_count'] ?? 0 }} dossiers
                </p>
            </div>
        </div>

        @if(Auth::user()->isAdmin())
        <!-- Gross Profit -->
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Marge brute</h3>
                <x-heroicon-o-arrow-trending-up class="h-4 w-4 text-muted-foreground" />
            </div>
            <div class="p-4 pt-0">
                <div class="text-xl sm:text-2xl font-bold">
                    @money($stats['gross_profit'] ?? 0)
                </div>
                <p class="text-xs text-muted-foreground mt-1">
                    Estimee selon le cout des composants
                </p>
            </div>
        </div>

        <!-- Net Cash Flow -->
        <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Tresorerie nette</h3>
                 <x-heroicon-o-chart-bar class="h-4 w-4 text-muted-foreground" />
            </div>
            <div class="p-4 pt-0">
                <div class="text-xl sm:text-2xl font-bold {{ ($stats['net_cash_flow'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    @money($stats['net_cash_flow'] ?? 0)
                </div>
                <div class="flex justify-between text-[11px] sm:text-xs text-muted-foreground mt-1">
                    <span class="text-emerald-600 flex items-center gap-1" title="Total encaissements">
                        <x-heroicon-s-arrow-up class="w-3 h-3" /> @money($stats['income'] ?? 0)
                    </span>
                    <span class="text-red-600 flex items-center gap-1" title="Total depenses">
                        <x-heroicon-s-arrow-down class="w-3 h-3" /> @money($stats['expense'] ?? 0)
                    </span>
                </div>
            </div>
        </div>
        @endif

         <!-- Low Stock Alert -->
         <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
            <div class="p-4 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Alerte stock critique</h3>
                <x-heroicon-o-exclamation-triangle class="h-4 w-4 text-orange-500" />
            </div>
            <div class="p-4 pt-0">
                <div class="text-xl sm:text-2xl font-bold">
                    {{ count($lowStockProducts) }}
                </div>
                <p class="text-xs text-muted-foreground mt-1">
                    Pieces sous le stock minimum
                </p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <!-- Sales Trend -->
        <div class="col-span-1 lg:col-span-2 rounded-xl border bg-card text-card-foreground shadow-sm break-inside-avoid">
            <div class="p-4 flex flex-col space-y-1.5 pb-2">
                <h3 class="font-semibold leading-none tracking-tight">Evolution des dossiers</h3>
                <p class="text-xs text-muted-foreground">Suivi quotidien des amenagements, renovations et SAV.</p>
            </div>
            <div class="p-4 pt-0" wire:ignore>
                <div id="salesChart" class="w-full h-[250px]"></div>
            </div>
        </div>

        @if(Auth::user()->isAdmin())
        <!-- Cash Flow -->
        <div class="col-span-1 rounded-xl border bg-card text-card-foreground shadow-sm break-inside-avoid">
            <div class="p-4 flex flex-col space-y-1.5 pb-2">
                <h3 class="font-semibold leading-none tracking-tight">Encaissements vs depenses</h3>
                <p class="text-xs text-muted-foreground">Vue financiere de l'activite atelier.</p>
            </div>
            <div class="p-4 pt-0" wire:ignore>
                <div id="cashFlowChart" class="w-full h-[250px]"></div>
            </div>
        </div>
        @endif
    </div>

    <!-- Data Tables Section -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <!-- Recent Sales -->
        <div class="col-span-1 lg:col-span-2 rounded-xl border bg-card text-card-foreground shadow-sm break-inside-avoid">
            <div class="p-4 flex flex-col space-y-1.5 border-b">
                <h3 class="font-semibold leading-none tracking-tight">Derniers dossiers ambulance</h3>
                <p class="text-xs text-muted-foreground">Amenagements, renovations et SAV recents.</p>
            </div>
            <div class="p-0">
                <div class="relative w-full overflow-auto max-h-[300px]">
                    <table class="w-full caption-bottom text-sm">
                        <thead class="[&_tr]:border-b sticky top-0 bg-card z-10">
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <th class="h-10 px-4 text-left align-middle font-medium text-muted-foreground">Facture</th>
                                <th class="h-10 px-4 text-right align-middle font-medium text-muted-foreground">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0 bg-transparent">
                            @forelse($recentSales as $sale)
                                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <td class="px-4 py-2 align-middle font-medium">
                                        {{ $sale['invoice_number'] }}
                                        <div class="text-[11px] text-muted-foreground font-normal">{{ $sale['customer']['name'] ?? 'Client passager' }}</div>
                                    </td>
                                    <td class="px-4 py-2 align-middle text-right font-medium text-emerald-600">@money($sale['total'])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="p-4 text-center text-muted-foreground">Aucun dossier recent.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(Auth::user()->isAdmin())
        <!-- Expense Breakdown -->
        <div class="col-span-1 rounded-xl border bg-card text-card-foreground shadow-sm break-inside-avoid">
            <div class="p-4 flex flex-col space-y-1.5 pb-2">
                <h3 class="font-semibold leading-none tracking-tight">Repartition des depenses</h3>
                <p class="text-xs text-muted-foreground">Distribution par categorie.</p>
            </div>
            <div class="p-4 pt-0" wire:ignore>
                <div id="expenseChart" class="w-full h-[250px] flex items-center justify-center"></div>
            </div>
        </div>
        @endif
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-2">
        <!-- Top Selling Products -->
        <div class="col-span-1 rounded-xl border bg-card text-card-foreground shadow-sm break-inside-avoid">
            <div class="p-4 flex flex-col space-y-1.5 border-b">
                <h3 class="font-semibold leading-none tracking-tight">Pieces les plus utilisees</h3>
                <p class="text-xs text-muted-foreground">Composants les plus consommes en atelier.</p>
            </div>
             <div class="p-4 pt-4 max-h-[300px] overflow-auto">
                <div class="space-y-4">
                    @forelse($topProducts as $product)
                        <div class="flex items-center justify-between">
                            <div class="space-y-1 flex-1">
                                <p class="text-sm font-medium leading-none truncate pr-2" title="{{ $product['product_name'] }}">{{ $product['product_name'] }}</p>
                                <p class="text-[11px] text-muted-foreground">{{ $product['sku'] }}</p>
                            </div>
                            <div class="font-semibold text-sm bg-muted px-2 py-1 rounded-md">
                                {{ $product['total_sold'] }} <span class="text-xs font-normal text-muted-foreground">utilisees</span>
                            </div>
                        </div>
                    @empty
                         <p class="text-xs text-muted-foreground text-center py-2">Aucune donnee produit.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="col-span-1 rounded-xl border bg-card text-card-foreground shadow-sm break-inside-avoid">
            <div class="p-4 flex flex-col space-y-1.5 border-b">
                <h3 class="font-semibold leading-none tracking-tight">Top clients</h3>
                <p class="text-xs text-muted-foreground">Par chiffre d'affaires.</p>
            </div>
             <div class="p-4 pt-4 max-h-[300px] overflow-auto">
                <div class="space-y-4">
                    @forelse($topCustomers as $customer)
                        <div class="flex items-center justify-between">
                            <div class="space-y-1 flex-1">
                                <p class="text-sm font-medium leading-none truncate pr-2" title="{{ $customer['customer_name'] }}">{{ $customer['customer_name'] }}</p>
                                <p class="text-[11px] text-muted-foreground">{{ $customer['phone'] }}</p>
                            </div>
                            <div class="font-semibold text-sm text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md whitespace-nowrap">
                                @money($customer['total_spent'])
                            </div>
                        </div>
                    @empty
                         <p class="text-xs text-muted-foreground text-center py-2">Aucune donnee client.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page { size: landscape; margin: 1cm; }
        body { background-color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .print\:hidden { display: none !important; }
        .bg-card { border: 1px solid #e2e8f0; box-shadow: none !important; }
        .grid { gap: 1rem !important; }
        /* Prevent charts and cards from breaking across pages */
        .break-inside-avoid { break-inside: avoid; page-break-inside: avoid; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        let salesChart = null;
        let cashFlowChart = null;
        
        const currencySymbol = "{{ \App\Models\Setting::get('currency_symbol', 'DT') }}";
        const currencyPosition = "{{ \App\Models\Setting::get('currency_position', 'left') }}";

        const formatMoney = (val) => {
            let num = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(val);
            return currencyPosition === 'left' ? currencySymbol + ' ' + num : num + ' ' + currencySymbol;
        };

        const initCharts = (data) => {
            // Sales Chart
            const salesOptions = {
                series: [{
                    name: 'Dossiers',
                    data: data.sales.data
                }],
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    parentHeightOffset: 0
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                xaxis: {
                    categories: data.sales.labels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { cssClass: 'text-[10px] text-muted-foreground' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { cssClass: 'text-[10px] text-muted-foreground' }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                             return formatMoney(val);
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                        stops: [0, 90, 100]
                    }
                },
                colors: ['#0ea5e9'], // Sky 500
                tooltip: {
                    y: {
                        formatter: function (val) {
                             return formatMoney(val);
                        }
                    }
                }
            };

            // Cash Flow Chart
            const cashFlowOptions = {
                series: [{
                    name: 'Encaissements',
                    data: data.cashFlow.income
                }, {
                    name: 'Depenses',
                    data: data.cashFlow.expense
                }],
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    parentHeightOffset: 0
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ['transparent'] },
                xaxis: {
                    categories: data.cashFlow.labels,
                    labels: {
                        style: { cssClass: 'text-[10px] text-muted-foreground' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { cssClass: 'text-[10px] text-muted-foreground' },
                        formatter: (val) => {
                             // Shorten detailed numbers for y-axis
                             if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                             if (val >= 1000) return (val / 1000).toFixed(0) + 'k';
                             return val;
                        }
                    }
                },
                colors: ['#10b981', '#ef4444'], // Emerald 500, Red 500
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function (val) {
                             return formatMoney(val);
                        }
                    }
                }
            };

            // Expense Breakdown Chart
            const hasExpenseData = data.expense.series && data.expense.series.length > 0;
            const expenseOptions = {
                series: hasExpenseData ? data.expense.series.map(Number) : [1],
                labels: hasExpenseData ? data.expense.labels : ['Aucune donnee'],
                chart: {
                    type: 'donut',
                    height: 250,
                    fontFamily: 'inherit',
                    parentHeightOffset: 0
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                },
                dataLabels: { enabled: false },
                colors: hasExpenseData ? ['#ef4444', '#f97316', '#f59e0b', '#84cc16', '#06b6d4', '#6366f1'] : ['#e5e7eb'],
                tooltip: {
                    enabled: hasExpenseData,
                    y: {
                        formatter: function (val) {
                             return formatMoney(val);
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    offsetY: 0,
                    height: 60,
                }
            };

            if (salesChart) salesChart.destroy();
            if (cashFlowChart) cashFlowChart.destroy();
            if (window.expenseChartInst) window.expenseChartInst.destroy();

            salesChart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
            salesChart.render();

            cashFlowChart = new ApexCharts(document.querySelector("#cashFlowChart"), cashFlowOptions);
            cashFlowChart.render();
            
            window.expenseChartInst = new ApexCharts(document.querySelector("#expenseChart"), expenseOptions);
            window.expenseChartInst.render();
        };

        // Initial Load
        initCharts({
            sales: @json($salesChart),
            cashFlow: @json($cashFlowChart),
            expense: @json($expenseChart)
        });



        // Listen for server-side updates
        Livewire.on('stats-updated', (data) => {
             initCharts(data[0]); // data is array of args
        });
    });
</script>
</div>
