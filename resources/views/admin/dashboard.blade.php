@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Students Card -->
                <div class="group bg-card rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-border-card overflow-hidden dark:shadow-md">
                    <div class="p-6 relative">
                        <!-- Accent stripe -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-border-card"></div>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-border-card rounded-2xl flex items-center justify-center">
                                <i class="fas fa-user-graduate text-brand text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-brand mb-1">
                                    {{ $stats['total_students'] }}
                                </div>
                                <div class="text-sm font-medium text-text-secondary">Total Students</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Active Learners</span>
                            <div class="flex items-center text-emerald-600 dark:text-emerald-400">
                                <i class="fas fa-chart-line text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Instructors Card -->
                <div class="group bg-card rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-border-card overflow-hidden dark:shadow-md">
                    <div class="p-6 relative">
                        <!-- Accent stripe -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-400 to-indigo-500"></div>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-text-primary mb-1">
                                    {{ $stats['total_instructors'] }}
                                </div>
                                <div class="text-sm font-medium text-text-secondary">Total Instructors</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-blue-600 dark:text-blue-400 font-medium">Teaching Staff</span>
                            <div class="flex items-center text-blue-600 dark:text-blue-400">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Subjects Card -->
                <div class="group bg-card rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-border-card overflow-hidden dark:shadow-md">
                    <div class="p-6 relative">
                        <!-- Accent stripe -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-400 to-orange-500"></div>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-book text-white text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-text-primary mb-1">
                                    {{ $stats['total_subjects'] }}
                                </div>
                                <div class="text-sm font-medium text-text-secondary">Total Subjects</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">Active Courses</span>
                            <div class="flex items-center text-amber-600 dark:text-amber-400">
                                <i class="fas fa-layer-group text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Users Card -->
                <div class="group bg-card rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-border-card overflow-hidden dark:shadow-md">
                    <div class="p-6 relative">
                        <!-- Accent stripe -->
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-teal-400 to-cyan-500"></div>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-teal-400 to-cyan-500 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-users text-white text-2xl"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-text-primary mb-1">
                                    {{ $stats['total_users'] }}
                                </div>
                                <div class="text-sm font-medium text-text-secondary">Total Users</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-teal-600 dark:text-teal-400 font-medium">System Users</span>
                            <div class="flex items-center text-teal-600 dark:text-teal-400">
                                <i class="fas fa-user-circle text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Section -->
            <div class="bg-card rounded-2xl shadow-sm border border-border-card overflow-hidden dark:shadow-md">
                <div class="bg-gradient-to-r from-border-card to-brand p-1">
                    <div class="bg-card rounded-xl">
                        <div class="p-8">
                            <div class="flex items-center space-x-6">
                                <div class="relative">
                                    <div class="w-20 h-20 bg-border-card rounded-2xl flex items-center justify-center shadow-xl">
                                        <i class="fas fa-crown text-brand text-3xl"></i>
                                    </div>
                                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center">
                                        <i class="fas fa-star text-yellow-800 text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h2 class="text-3xl font-bold text-brand mb-2">
                                        Welcome Back, Admin! 👋
                                    </h2>
                                    <p class="text-lg text-text-secondary mb-4">
                                        Your gamified learning system is running smoothly. Here's your overview.
                                    </p>
                                    <div class="flex flex-wrap gap-4">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-border-card/30 text-brand">
                                            <i class="fas fa-gamepad mr-2"></i>
                                            Gamified Learning
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                            <i class="fas fa-chart-line mr-2"></i>
                                            System Active
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🌸 Quick Actions Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Manage Users -->
                <a href="{{ route('admin.student.index') }}" 
                class="block bg-card rounded-2xl shadow-sm border border-border-card p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-300 group dark:shadow-md">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-border-card rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user-plus text-brand"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-brand">Manage Users</h3>
                    </div>
                    <p class="text-text-secondary text-sm">Add, edit, or remove students and instructors from your system.</p>
                </a>

                <!-- Course Management -->
                <a href="{{ route('admin.subjects.index') }}" 
                class="block bg-card rounded-2xl shadow-sm border border-border-card p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-300 group dark:shadow-md">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-book-open text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-brand">Course Management</h3>
                    </div>
                    <p class="text-text-secondary text-sm">Create and organize subjects, modules, and learning materials.</p>
                </a>

                <!-- Analytics -->
                <a href="{{ route('admin.reports.index') }}" 
                class="block bg-card rounded-2xl shadow-sm border border-border-card p-6 hover:shadow-lg hover:scale-[1.02] transition-all duration-300 group dark:shadow-md">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-chart-bar text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-brand">Analytics</h3>
                    </div>
                    <p class="text-text-secondary text-sm">View detailed reports and insights about system performance.</p>
                </a>
            </div>

    
@endsection