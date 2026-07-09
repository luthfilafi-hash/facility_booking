<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Facilities (Laravel Version)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Use the existing CSS from the main app -->
    <link rel="stylesheet" href="/css/sportbook.css">
</head>
<body>

<!-- Header exactly like the original -->
<header class="sb-topnav">
    <a href="/index.php" class="sb-topnav-brand">
        <span class="brand-icon">
            <!-- Basic inline SVG for the brand icon so we don't need the icons.php helper -->
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
        </span>
        UniReserve (Laravel)
    </a>
    <nav class="sb-topnav-links">
        <a href="/index.php">Return to Native PHP App</a>
    </nav>
</header>

<div class="sb-hero" style="min-height: auto; padding: 3rem 2.5rem 1rem; background: linear-gradient(135deg, rgba(15,17,26,0.95), rgba(15,17,26,0.8));">
    <div class="sb-container">
        <a href="/index.php" class="sb-btn sb-btn-outline sb-btn-sm" style="border-radius: 999px; margin-bottom: 1.5rem; display: inline-flex; border-color: rgba(255,255,255,0.2);">
            &larr; Back to Native Homepage
        </a>
        <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem; text-align: left;">Laravel Facilities View</h1>
        <p style="color: var(--muted-foreground); font-size: 1.1rem; text-align: left;">Rendered using Laravel's Blade engine and Eloquent Database Builder</p>
    </div>
</div>

<section class="sb-section">
    <div class="sb-container">
        <div class="sb-facilities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            @foreach($facilities as $f)
            <div class="sb-facility-card" style="border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.03); transition: all 0.2s;">
                @if(!empty($f->image_path))
                    <img src="/{{ $f->image_path }}" alt="{{ $f->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                @else
                    <div class="sb-facility-thumb" style="height: 200px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0a1628, #1a3a4a);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
                    </div>
                @endif
                <div class="sb-facility-body" style="padding: 1.5rem;">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $f->name }}</h3>
                    <div class="loc" style="display: flex; align-items: center; gap: 0.4rem; color: var(--muted-foreground); font-size: 0.85rem; margin-bottom: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $f->location }}
                    </div>
                    <p style="font-size: 0.9rem; color: var(--muted-foreground); margin-bottom: 1.5rem;">Capacity: {{ $f->capacity ?? 'N/A' }} people</p>
                    <a href="/availability.php" class="sb-btn sb-btn-outline" style="width: 100%; justify-content: center;">Check Availability</a>
                </div>
            </div>
            @endforeach
            
            @if(count($facilities) === 0)
                <p style="text-align:center; color: var(--muted-foreground); grid-column: 1/-1;">No facilities available.</p>
            @endif
        </div>
    </div>
</section>

</body>
</html>
