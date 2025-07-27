<x-app-layout>
    <script>
        function companyAutocomplete() {
            return {
                searchTerm: '{{ old("company_name", "") }}',
                selectedCompanyId: '{{ old("company_id", "") }}',
                selectedCompanyName: '{{ old("company_name", "") }}',
                showDropdown: false,
                selectedIndex: -1,
                companies: @json($companies),
                filteredCompanies: [],
                
                searchCompanies() {
                    if (this.searchTerm.length < 2) {
                        this.filteredCompanies = [];
                        this.selectedIndex = -1;
                        return;
                    }
                    
                    const term = this.searchTerm.toLowerCase();
                    this.filteredCompanies = this.companies.filter(company => 
                        company.name.toLowerCase().includes(term) ||
                        company.website.toLowerCase().includes(term)
                    ).slice(0, 8); // Limit to 8 results
                    
                    this.selectedIndex = -1;
                },
                
                selectNext() {
                    if (this.selectedIndex < this.filteredCompanies.length - 1) {
                        this.selectedIndex++;
                    }
                },
                
                selectPrevious() {
                    if (this.selectedIndex > 0) {
                        this.selectedIndex--;
                    } else if (this.selectedIndex === 0) {
                        this.selectedIndex = -1;
                    }
                },
                
                selectCompany() {
                    if (this.selectedIndex >= 0 && this.filteredCompanies[this.selectedIndex]) {
                        this.selectCompanyFromList(this.filteredCompanies[this.selectedIndex]);
                    } else if (this.searchTerm.trim()) {
                        // If no company is selected but user typed something, use it as custom company
                        this.selectedCompanyId = '';
                        this.selectedCompanyName = this.searchTerm.trim();
                        this.showDropdown = false;
                    }
                },
                
                selectCompanyFromList(company) {
                    this.searchTerm = company.name;
                    this.selectedCompanyId = company.id;
                    this.selectedCompanyName = company.name;
                    this.showDropdown = false;
                    this.selectedIndex = -1;
                },
                
                init() {
                    // If we have a selected company ID from old input, find the company name
                    if (this.selectedCompanyId) {
                        const company = this.companies.find(c => c.id == this.selectedCompanyId);
                        if (company) {
                            this.searchTerm = company.name;
                            this.selectedCompanyName = company.name;
                        }
                    }
                    
                    // Watch for changes in searchTerm to update company_name for custom companies
                    this.$watch('searchTerm', (value) => {
                        if (value && !this.selectedCompanyId) {
                            this.selectedCompanyName = value;
                        }
                    });
                },
                
                submitForm(event) {
                    // Ensure company_name is set before form submission
                    if (this.searchTerm && !this.selectedCompanyName) {
                        this.selectedCompanyName = this.searchTerm;
                    }
                }
            }
        }

        function jobRoleAutocomplete() {
            return {
                searchTerm: '{{ old("job_title", "") }}',
                selectedRole: '{{ old("job_title", "") }}',
                showDropdown: false,
                selectedIndex: -1,
                roles: @json($jobRoles),
                filteredRoles: [],
                
                searchRoles() {
                    if (this.searchTerm.length < 2) {
                        this.filteredRoles = [];
                        this.selectedIndex = -1;
                        return;
                    }
                    
                    const term = this.searchTerm.toLowerCase();
                    this.filteredRoles = this.roles.filter(role => 
                        role.toLowerCase().includes(term)
                    ).slice(0, 10); // Limit to 10 results
                    
                    this.selectedIndex = -1;
                },
                
                selectNext() {
                    if (this.selectedIndex < this.filteredRoles.length - 1) {
                        this.selectedIndex++;
                    }
                },
                
                selectPrevious() {
                    if (this.selectedIndex > 0) {
                        this.selectedIndex--;
                    } else if (this.selectedIndex === 0) {
                        this.selectedIndex = -1;
                    }
                },
                
                selectRole() {
                    if (this.selectedIndex >= 0 && this.filteredRoles[this.selectedIndex]) {
                        this.selectRoleFromList(this.filteredRoles[this.selectedIndex]);
                    } else if (this.searchTerm.trim()) {
                        // If no role is selected but user typed something, use it as custom role
                        this.selectedRole = this.searchTerm.trim();
                        this.showDropdown = false;
                    }
                },
                
                selectRoleFromList(role) {
                    this.searchTerm = role;
                    this.selectedRole = role;
                    this.showDropdown = false;
                    this.selectedIndex = -1;
                },
                
                init() {
                    // If we have an old job title, set it
                    if (this.selectedRole) {
                        this.searchTerm = this.selectedRole;
                    }
                },
                
                submitForm(event) {
                    // Ensure job_title is set before form submission
                    if (this.searchTerm && !this.selectedRole) {
                        this.selectedRole = this.searchTerm;
                    }
                }
            }
        }
    </script>
    
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 lg:p-8">
                    {{-- Header --}}
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-deep-slate dark:text-gray-200">Add New Application</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Track a new job application</p>
                    </div>

                    {{-- Form --}}
                    <form method="POST" action="{{ route('applications.store') }}" class="space-y-6" x-data="formHandler()" @submit="submitForm">
                        @csrf

                        {{-- Company Autocomplete --}}
                        <div x-data="companyAutocomplete()" x-init="init()" class="relative">
                            <label for="company_search" class="block text-sm font-medium text-deep-slate dark:text-gray-200 mb-2">
                                Company *
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="company_search" 
                                       x-model="searchTerm"
                                       @input="searchCompanies()"
                                       @focus="showDropdown = true"
                                       @click.away="showDropdown = false"
                                       @keydown.arrow-down.prevent="selectNext()"
                                       @keydown.arrow-up.prevent="selectPrevious()"
                                       @keydown.enter.prevent="selectCompany()"
                                       @keydown.escape="showDropdown = false"
                                       :class="(selectedCompanyId || selectedCompanyName) ? 'border-sage-green bg-sage-green/5' : 'border-gray-300 dark:border-gray-600'"
                                       class="w-full px-3 py-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:border-sage-green dark:bg-gray-700 dark:text-gray-200"
                                       placeholder="Start typing company name..."
                                       required>
                                
                                {{-- Selected company indicator --}}
                                <div x-show="selectedCompanyId || (selectedCompanyName && !companies.find(c => c.name.toLowerCase() === selectedCompanyName.toLowerCase()))" 
                                     x-transition
                                     class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <div class="flex items-center space-x-2">
                                        <template x-if="selectedCompanyId">
                                            <img :src="companies.find(c => c.id == selectedCompanyId)?.logo_url" 
                                                 :alt="selectedCompanyName + ' logo'"
                                                 class="w-5 h-5 rounded object-cover"
                                                 @@error="$el.src='https://via.placeholder.com/20x20/6B7280/FFFFFF?text=' + selectedCompanyName.charAt(0)">
                                        </template>
                                        <template x-if="!selectedCompanyId && selectedCompanyName">
                                            <div class="w-5 h-5 rounded bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400" x-text="selectedCompanyName.charAt(0).toUpperCase()"></span>
                                            </div>
                                        </template>
                                        <svg class="w-4 h-4 text-sage-green" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Hidden fields for form submission --}}
                            <input type="hidden" name="company_id" x-model="selectedCompanyId">
                            <input type="hidden" name="company_name" x-model="selectedCompanyName">
                            
                            {{-- Autocomplete Dropdown --}}
                            <div x-show="showDropdown && filteredCompanies.length > 0" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="(company, index) in filteredCompanies" :key="company.id">
                                    <div @click="selectCompanyFromList(company)"
                                         @mouseenter="selectedIndex = index"
                                         :class="{'bg-sage-green/10': selectedIndex === index}"
                                         class="px-4 py-3 cursor-pointer hover:bg-sage-green/10 flex items-center space-x-3">
                                        {{-- Company Logo --}}
                                        <div class="flex-shrink-0">
                                            <img :src="company.logo_url" 
                                                 :alt="company.name + ' logo'"
                                                 class="w-8 h-8 rounded object-cover border border-gray-200 dark:border-gray-600"
                                                 @@error="$el.src='https://via.placeholder.com/32x32/6B7280/FFFFFF?text=' + company.name.charAt(0)">
                                        </div>
                                        {{-- Company Name --}}
                                        <div>
                                            <div class="font-medium text-deep-slate dark:text-gray-200" x-text="company.name"></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400" x-text="company.website"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Job Title Autocomplete --}}
                        <div x-data="jobRoleAutocomplete()" x-init="init()" class="relative">
                            <label for="job_title_search" class="block text-sm font-medium text-deep-slate dark:text-gray-200 mb-2">
                                Job Title
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="job_title_search" 
                                       x-model="searchTerm"
                                       @input="searchRoles()"
                                       @focus="showDropdown = true"
                                       @click.away="showDropdown = false"
                                       @keydown.arrow-down.prevent="selectNext()"
                                       @keydown.arrow-up.prevent="selectPrevious()"
                                       @keydown.enter.prevent="selectRole()"
                                       @keydown.escape="showDropdown = false"
                                       :class="selectedRole ? 'border-sage-green bg-sage-green/5' : 'border-gray-300 dark:border-gray-600'"
                                       class="w-full px-3 py-2 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:border-sage-green dark:bg-gray-700 dark:text-gray-200"
                                       placeholder="Start typing job title...">
                                
                                {{-- Selected role indicator --}}
                                <div x-show="selectedRole" 
                                     x-transition
                                     class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-4 h-4 text-sage-green" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            {{-- Hidden field for form submission --}}
                            <input type="hidden" name="job_title" x-model="selectedRole">
                            
                            {{-- Autocomplete Dropdown --}}
                            <div x-show="showDropdown && filteredRoles.length > 0" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <template x-for="(role, index) in filteredRoles" :key="index">
                                    <div @click="selectRoleFromList(role)"
                                         @mouseenter="selectedIndex = index"
                                         :class="{'bg-sage-green/10': selectedIndex === index}"
                                         class="px-4 py-3 cursor-pointer hover:bg-sage-green/10">
                                        <div class="font-medium text-deep-slate dark:text-gray-200" x-text="role"></div>
                                    </div>
                                </template>
                            </div>
                            
                            @error('job_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Salary --}}
                        <div>
                            <label for="salary" class="block text-sm font-medium text-deep-slate dark:text-gray-200 mb-2">
                                Salary
                            </label>
                            <input type="text" 
                                   name="salary" 
                                   id="salary" 
                                   value="{{ old('salary') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:border-sage-green dark:bg-gray-700 dark:text-gray-200"
                                   placeholder="e.g., $50,000 - $70,000">
                            @error('salary')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-deep-slate dark:text-gray-200 mb-2">
                                Status
                            </label>
                            <select name="status" 
                                    id="status"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:border-sage-green dark:bg-gray-700 dark:text-gray-200">
                                <option value="Applied" {{ old('status') == 'Applied' ? 'selected' : '' }}>Applied</option>
                                <option value="Interviewing" {{ old('status') == 'Interviewing' ? 'selected' : '' }}>Interviewing</option>
                                <option value="Offer" {{ old('status') == 'Offer' ? 'selected' : '' }}>Offer</option>
                                <option value="Rejected" {{ old('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="Withdrawn" {{ old('status') == 'Withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Application Date --}}
                        <div>
                            <label for="application_date" class="block text-sm font-medium text-deep-slate dark:text-gray-200 mb-2">
                                Application Date
                            </label>
                            <input type="date" 
                                   name="application_date" 
                                   id="application_date" 
                                   value="{{ old('application_date', date('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:border-sage-green dark:bg-gray-700 dark:text-gray-200">
                            @error('application_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Follow Up Date --}}
                        <div>
                            <label for="follow_up_date" class="block text-sm font-medium text-deep-slate dark:text-gray-200 mb-2">
                                Follow Up Date
                            </label>
                            <input type="date" 
                                   name="follow_up_date" 
                                   id="follow_up_date" 
                                   value="{{ old('follow_up_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:border-sage-green dark:bg-gray-700 dark:text-gray-200">
                            @error('follow_up_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contact Email --}}
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-deep-slate dark:text-gray-200 mb-2">
                                Contact Email
                            </label>
                            <input type="email" 
                                   name="contact_email" 
                                   id="contact_email" 
                                   value="{{ old('contact_email') }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:border-sage-green dark:bg-gray-700 dark:text-gray-200"
                                   placeholder="recruiter@company.com">
                            @error('contact_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label for="notes" class="block text-sm font-medium text-deep-slate dark:text-gray-200 mb-2">
                                Notes
                            </label>
                            <textarea name="notes" 
                                      id="notes" 
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:border-sage-green dark:bg-gray-700 dark:text-gray-200"
                                      placeholder="Add any notes about this application...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Form Actions --}}
                        <div class="flex justify-end space-x-3 pt-4">
                            <a href="{{ route('applications.index') }}" 
                               class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-sage-green hover:bg-sage-green/90 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-sage-green/50 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Create Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 