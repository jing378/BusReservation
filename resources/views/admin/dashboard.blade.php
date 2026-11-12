<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bus Reservation System</title>
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/message-handler.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/weather-integration.css') }}">
    <style>
        /* Topbar styles */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .current-time {
            color: #94a3b8;
            font-size: 1rem;
            font-weight: 500;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .current-time {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .current-time {
                align-self: flex-end;
            }
        }

        /* Bus carousel styles */
        .bus-carousel {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            overflow: hidden;
        }

        .bus-carousel .carousel-dots {
            display: flex !important;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin: 0;
            position: relative;
            z-index: 5;
            padding: 0;
            width: 100%;
        }

        .bus-container {
            display: flex;
            transition: transform 0.5s ease;
        }

        .bus-card {
            min-width: 100%;
            background: #2a3441;
            border-radius: 12px;
            padding: 20px;
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }

        .bus-carousel:hover .carousel-btn {
            opacity: 1;
            visibility: visible;
        }

        .carousel-btn:hover {
            background: rgba(59, 130, 246, 0.2);
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-btn.prev {
            left: -20px;
        }

        .carousel-btn.next {
            right: -20px;
        }

        .bus-carousel:hover .carousel-btn.prev {
            left: 10px;
        }

        .bus-carousel:hover .carousel-btn.next {
            right: 10px;
        }

        /* Bus carousel dots */
        .bus-carousel .carousel-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            position: relative;
            z-index: 5;
            padding: 10px 0;
        }

        .bus-carousel .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
            margin: 0;
            display: block;
            flex-shrink: 0;
            border: none;
        }

        .bus-carousel .dot:hover {
            background: rgba(59, 130, 246, 0.5);
            transform: scale(1.1);
        }

        .bus-carousel .dot.active {
            background: #3b82f6;
            transform: scale(1.2);
        }
        
        /* Weather carousel dots */
        .weather-carousel .carousel-dots {
            display: flex !important;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            position: relative;
            z-index: 5;
            padding: 10px 0;
        }

        .weather-carousel .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
            margin: 0;
            display: block;
            flex-shrink: 0;
            border: none;
        }

        .weather-carousel .dot:hover {
            background: rgba(59, 130, 246, 0.5);
            transform: scale(1.1);
        }

        .weather-carousel .dot.active {
            background: #3b82f6;
            transform: scale(1.2);
        }

        .seat-layout-header {
            text-align: center;
            margin-bottom: 15px;
            color: #e5e7eb;
        }

        .seat-layout-header h4 {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0 0 4px 0;
            color: #3b82f6;
        }

        .bus-info {
            font-size: 0.8rem;
            color: #9ca3af;
            margin: 2px 0;
        }

        .route-info {
            font-size: 0.95rem;
            font-weight: 500;
            margin: 4px 0;
            color: #e5e7eb;
        }

        .departure-info {
            font-size: 0.8rem;
            color: #22c55e;
            margin: 2px 0;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr) 40px repeat(3, 1fr);
            gap: 6px;
            max-width: 480px;
            margin: 0 auto;
            grid-auto-rows: min-content;
        }

        .seat {
            aspect-ratio: 1;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e5e7eb;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .seat.available:hover {
            background: rgba(59,130,246,0.1);
            border-color: rgba(59,130,246,0.3);
            transform: scale(1.05);
        }

        .seat.occupied {
            background: rgba(239,68,68,0.1);
            border-color: rgba(239,68,68,0.3);
            color: #ef4444;
            cursor: not-allowed;
        }

        .aisle {
            background: transparent;
            pointer-events: none;
        }

        .seat-legend {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 12px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #b8c7db;
            font-size: 11px;
        }

        .legend-box {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        .legend-available { 
            background: rgba(255,255,255,0.04); 
            border: 1px solid rgba(255,255,255,0.08); 
        }

        .legend-booked { 
            background: rgba(239,68,68,0.1); 
            border: 1px solid rgba(239,68,68,0.3); 
        }

        /* Bus layout section styles */
        .bus-layout-section {
            padding: 1rem;
            background: var(--card-gradient, rgba(255,255,255,0.02));
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1.5rem;
        }

        /* Enhanced card styling */
        .card {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-badge.confirmed {
            background-color: #28a745;
            color: #fff;
        }
        
        .status-badge.pending {
            background-color: #a855f7;
            color: #fff;
        }
        
        .status-badge.cancelled {
            background-color: #dc3545;
            color: #fff;
        }
        
        .status-badge.completed {
            background-color: #17a2b8;
            color: #fff;
        }
        
        .status-badge.departed {
            background-color: #6366f1;
            color: #fff;
        }
        
        .status-badge.scheduled {
            background-color: #ffc107;
            color: #000;
        }
        
        .status-badge.active {
            background-color: #28a745;
            color: #fff;
        }
        
        .status-confirmed { background: rgba(34,197,94,0.1); color: #22c55e; }
        .status-pending { background: rgba(234,179,8,0.1); color: #eab308; }
        .status-cancelled { background: rgba(239,68,68,0.1); color: #ef4444; }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 600px), 1fr));
            gap: 8px;
            margin: 6px 0;
            max-width: 100%;
            overflow: hidden;
        }
        
        .panel {
            margin-bottom: 13px;
        }
        
        .panel h3 {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #e5e7eb;
        }
        
        .panel h3 i {
            font-size: 1rem;
        }
        
        .table-wrap {
            background: rgba(255,255,255,0.02);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.05);
            overflow: hidden;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background: rgba(255,255,255,0.02);
            padding: 12px;
            font-size: 13px;
            font-weight: 500;
            text-align: left;
            color: #94a3b8;
        }
        
        .table td {
            padding: 12px;
            font-size: 13px;
            border-top: 1px solid rgba(255,255,255,0.05);
            color: #e5e7eb;
        }
        
        .table tbody tr:hover {
            background: rgba(255,255,255,0.02);
        }

        .text-blue-500 { color: #3b82f6; }
        .text-green-500 { color: #22c55e; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(59,130,246,0.1);
            color: #3b82f6;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn:hover {
            background: rgba(59,130,246,0.2);
            transform: translateY(-1px);
        }
        
        /* Main layout grid */
        .main-layout {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 20px;
        }

        /* Ensure main layout adjusts when sidebar is collapsed */
        .dash-wrap.collapsed .main-layout {
            grid-template-columns: 400px 1fr;
        }

        /* Ensure content area adjusts properly when sidebar is collapsed */
        .dash-wrap.collapsed .content {
            margin-left: 0;
        }

        /* Ensure smooth transitions for sidebar collapse */
        .main-layout {
            transition: grid-template-columns 0.25s ease;
        }

        /* Ensure bus layout section adjusts when sidebar is collapsed */
        .dash-wrap.collapsed .bus-layout-section {
            max-width: 100%;
        }

        /* Ensure dashboard grid adjusts when sidebar is collapsed */
        .dash-wrap.collapsed .dashboard-grid {
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 600px), 1fr));
        }

        /* Prevent horizontal overflow */
        .content {
            overflow-x: hidden;
            max-width: 100%;
        }

        /* Make bus layout section more responsive */
        .bus-layout-section {
            max-width: 100%;
            overflow: hidden;
        }

        /* Ensure bus carousel doesn't cause overflow */
        .bus-carousel {
            max-width: 100%;
            overflow: hidden;
        }

        /* Chart containers to prevent vertical expansion */
        .chart-wrap { 
            height: 260px; 
            max-width: 100%;
            overflow: hidden;
        }
        .chart-wrap--sm { 
            height: 220px; 
            max-width: 100%;
            overflow: hidden;
        }

        /* Ensure canvas elements are responsive */
        .chart-wrap canvas {
            max-width: 100% !important;
            height: auto !important;
        }
        
        /* Force legend text color */
        .chartjs-legend-text {
            color: #ffffff !important;
        }

        /* Responsive charts container */
        .charts-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            align-items: stretch;
            max-width: 100%;
            overflow: hidden;
        }

        .chart-section {
            min-width: 0; /* Allow flex shrinking */
            overflow: hidden;
        }

        /* Stack the two charts only on narrow screens */
        @media (max-width: 760px) {
            .charts-container {
                grid-template-columns: minmax(0, 1fr);
                gap: 20px;
            }
        }

        /* Comprehensive Responsive Design */
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .main-layout {
                grid-template-columns: 1fr !important;
            }
            
            .bus-layout-section {
                margin-bottom: 20px;
            }
        }

        /* Ensure sidebar collapse works properly on all screen sizes */
        @media (max-width: 980px) {
            .dash-wrap.collapsed .main-layout {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 992px) {
            .main-layout {
                grid-template-columns: 1fr !important;
                gap: 15px;
            }
            
            .bus-carousel {
                max-width: 100%;
            }
            
            .seat-grid {
                grid-template-columns: repeat(3, 1fr) 30px repeat(3, 1fr);
                gap: 4px;
            }
            
            .status-badge {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
            }
        }

        @media (max-width: 768px) {
            .main-layout {
                grid-template-columns: 1fr !important;
                gap: 10px;
            }
            
            .bus-layout-section {
                padding: 0.75rem;
            }
            
            .seat-grid {
                grid-template-columns: repeat(2, 1fr) 20px repeat(2, 1fr);
                gap: 3px;
            }
            
            .seat {
                font-size: 10px;
            }
            
            .carousel-btn {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }
            
            .carousel-btn.prev {
                left: -16px;
            }
            
            .carousel-btn.next {
                right: -16px;
            }
            
            .bus-carousel:hover .carousel-btn.prev {
                left: 5px;
            }
            
            .bus-carousel:hover .carousel-btn.next {
                right: 5px;
            }
            
            .table {
                font-size: 12px;
            }
            
            .table th,
            .table td {
                padding: 8px;
            }
            
            .panel h3 {
                font-size: 1rem;
            }
            
            .status-badge {
                font-size: 0.65rem;
                padding: 0.15rem 0.35rem;
            }
        }

        @media (max-width: 576px) {
            .main-layout {
                margin: 10px 0;
                gap: 8px;
            }
            
            .bus-layout-section {
                padding: 0.5rem;
            }
            
            .seat-grid {
                grid-template-columns: repeat(2, 1fr) 15px repeat(2, 1fr);
                gap: 2px;
            }
            
            .seat {
                font-size: 9px;
                border-radius: 4px;
            }
            
            .seat-layout-header h4 {
                font-size: 1rem;
            }
            
            .bus-info,
            .route-info,
            .departure-info {
                font-size: 0.7rem;
            }
            
            .legend-item {
                font-size: 9px;
                gap: 4px;
            }
            
            .legend-box {
                width: 8px;
                height: 8px;
            }
            
            .table {
                font-size: 11px;
            }
            
            .table th,
            .table td {
                padding: 6px 4px;
            }
            
            .panel {
                padding: 0.75rem;
            }
            
            .panel h3 {
                font-size: 0.9rem;
                margin-bottom: 0.75rem;
            }
            
            .status-badge {
                font-size: 0.6rem;
                padding: 0.1rem 0.3rem;
            }
            
            .btn {
                padding: 8px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .main-layout {
                margin: 5px 0;
                gap: 5px;
            }
            
            .bus-layout-section {
                padding: 0.4rem;
            }
            
            .seat-grid {
                grid-template-columns: repeat(2, 1fr) 10px repeat(2, 1fr);
                gap: 1px;
            }
            
            .seat {
                font-size: 8px;
                border-radius: 3px;
            }
            
            .table {
                font-size: 10px;
            }
            
            .table th,
            .table td {
                padding: 4px 2px;
            }
            
            .panel {
                padding: 0.5rem;
            }
            
            .panel h3 {
                font-size: 0.8rem;
                margin-bottom: 0.5rem;
            }
            
            .status-badge {
                font-size: 0.55rem;
                padding: 0.1rem 0.3rem;
            }
            
            .btn {
                padding: 6px 10px;
                font-size: 11px;
            }
        }

        /* Weather Carousel Styles */
        .weather-carousel {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .weather-container {
            display: flex;
            transition: transform 0.5s ease;
            width: 100%;
        }

        .weather-slide {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            min-width: 100%;
            padding: 0.75rem 0.5rem;
        }

        .weather-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 0.85rem;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            min-height: 185px;
            gap: 0.35rem;
        }

        .weather-carousel .weather-card-header h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #e5e7eb;
            margin: 0 0 0.75rem 0;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .weather-carousel .weather-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.35rem;
        }

        .weather-carousel .weather-info img {
            width: 40px !important;
            height: 40px !important;
            margin-bottom: -0.25rem;
        }

        .weather-carousel .temperature {
            font-size: 1.35rem;
            font-weight: 600;
            color: #3b82f6;
            line-height: 1;
        }

        .weather-carousel .condition {
            color: #94a3b8;
            text-align: center;
            font-size: 0.85rem;
            margin-bottom: 0.35rem;
            line-height: 1.2;
        }

        .weather-carousel .weather-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            width: 100%;
            margin-top: 0.35rem;
            padding-top: 0.35rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .weather-carousel .weather-detail-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.2rem;
            font-size: 0.75rem;
            color: #94a3b8;
            line-height: 1;
        }

        .weather-carousel .weather-detail-item i {
            font-size: 0.9rem;
            margin-bottom: 0.1rem;
        }

        .weather-carousel .weather-detail-item small {
            font-size: 0.7rem;
            opacity: 0.8;
        }

        .weather-advisory {
            margin-top: auto;
            padding: 0.35rem 0.5rem;
            font-size: 0.7rem;
            color: #eab308;
            text-align: center;
            background: rgba(234, 179, 8, 0.05);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            line-height: 1.2;
            margin-top: 0.5rem;
        }

        .weather-advisory i {
            font-size: 0.75rem;
        }

        .weather-advisory span {
            flex: 1;
        }

        .weather-carousel .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }

        .weather-carousel:hover .carousel-btn {
            opacity: 1;
            visibility: visible;
        }

        .weather-carousel .carousel-btn.prev {
            left: -20px;
        }

        .weather-carousel .carousel-btn.next {
            right: -20px;
        }

        .weather-carousel:hover .carousel-btn.prev {
            left: 10px;
        }

        .weather-carousel:hover .carousel-btn.next {
            right: 10px;
        }

        /* Show carousel elements when needed via JS */
        .weather-carousel.has-multiple-slides .carousel-btn,
        .weather-carousel.has-multiple-slides .carousel-dots {
            display: flex;
        }

        @media (max-width: 1400px) {
            .weather-slide {
                gap: 1rem;
            }
            
            .weather-carousel .temperature {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 1200px) {
            .weather-slide {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 1rem;
            }
        }

        @media (max-width: 576px) {
            .weather-slide {
                grid-template-columns: repeat(1, minmax(0, 1fr));
                gap: 0.75rem;
            }
            
            .weather-card {
                padding: 1rem;
            }
            
            .weather-carousel .weather-card-header h4 {
                font-size: 1rem;
                margin: 0 0 0.75rem 0;
            }
            
            .weather-carousel .temperature {
                font-size: 1.25rem;
            }
            
            .weather-carousel .condition {
                font-size: 0.85rem;
            }
        }
        
        /* Logout Confirmation Modal Styles */
        .confirm-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            backdrop-filter: blur(4px);
        }
        
        .confirm-modal-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #1a222c;
            border-radius: 12px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            z-index: 1001;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .confirm-modal-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        
        .confirm-modal-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(59, 130, 246, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            font-size: 18px;
        }
        
        .confirm-modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #e5e7eb;
            margin: 0;
        }
        
        .confirm-modal-body {
            color: #9ca3af;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .confirm-modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        
        .confirm-modal-btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 120px;
            justify-content: center;
        }
        
        .confirm-modal-btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #e5e7eb;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .confirm-modal-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        
        .confirm-modal-btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            border: 1px solid rgba(59, 130, 246, 0.6);
        }
        
        .confirm-modal-btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/mobile.css') }}">
    <script src="{{ asset('assets/js/mobile-responsive.js') }}" defer></script>
</head>
<body>
    <div class="dash-wrap">
        @include('partials.sidebar')
        <button class="btn-ghost hamburger-btn" id="mobileMenuToggle" title="Toggle menu"><i class="fas fa-bars"></i></button>
        <main class="content">
            <header class="dash-hero">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i class="fas fa-shield-halved"></i>
                        <span>Administrator</span>
                    </div>
                    <h1>Welcome back, {{ $full_name }}</h1>
                    <p>Monitor bookings, trips, buses, and revenue across your bus reservation system.</p>
                    <div class="hero-clock"><i class="fas fa-clock"></i> <span id="current-time"></span></div>
                </div>
                <section class="hero-aside" aria-labelledby="heroWeatherTitle">
                    <div class="hero-aside-head">
                        <span class="hero-aside-label" id="heroWeatherTitle"><i class="fas fa-cloud-sun"></i> Weather Conditions</span>
                    </div>
                    <div id="weather-alerts" aria-live="polite">
                        <div class="hero-weather-status">
                            <i class="fas fa-spinner fa-spin"></i> Loading weather information...
                        </div>
                    </div>
                </section>
            </header>
            <section class="kpis" aria-label="Key figures">
                <div class="kpi">
                    <span class="kpi-icon" style="--c:#3b82f6"><i class="fas fa-ticket"></i></span>
                    <div class="kpi-meta">
                        <span class="kpi-label">Today's Bookings</span>
                        <strong class="kpi-value">{{ number_format($countBookings) }}</strong>
                        <span class="kpi-sub">{{ number_format($cancellationsToday) }} cancelled · {{ number_format($totalBookings) }} all-time</span>
                    </div>
                </div>
                <div class="kpi">
                    <span class="kpi-icon" style="--c:#22c55e"><i class="fas fa-peso-sign"></i></span>
                    <div class="kpi-meta">
                        <span class="kpi-label">Revenue This Month</span>
                        <strong class="kpi-value">₱{{ number_format($monthlyRevenue, 2) }}</strong>
                        <span class="kpi-sub">₱{{ number_format($totalRevenue, 2) }} all-time</span>
                    </div>
                </div>
                <div class="kpi">
                    <span class="kpi-icon" style="--c:#f59e0b"><i class="fas fa-chair"></i></span>
                    <div class="kpi-meta">
                        <span class="kpi-label">Occupancy Today</span>
                        <strong class="kpi-value">{{ $occupancyRateToday }}%</strong>
                        <span class="kpi-sub">Avg. ticket ₱{{ number_format($avgTicketToday, 2) }}</span>
                    </div>
                    <span class="kpi-meter" style="--fill: {{ min(100, max(0, $occupancyRateToday)) }}%"></span>
                </div>
                <div class="kpi">
                    <span class="kpi-icon" style="--c:#22d3ee"><i class="fas fa-bus"></i></span>
                    <div class="kpi-meta">
                        <span class="kpi-label">Fleet In Service</span>
                        <strong class="kpi-value">{{ number_format($countBuses) }} buses</strong>
                        <span class="kpi-sub">{{ number_format($countBusesWithTrips) }} on trips · {{ number_format($countRoutes) }} routes</span>
                    </div>
                </div>
            </section>

            <div class="dash-grid">
                <section class="panel span-2 chart-panel">
                    <div class="charts-container">
                        <div class="chart-section">
                            <h3 style="margin-bottom:12px;"><i class="fas fa-chart-line text-blue-500"></i> Bookings & Revenue</h3>
                            <p style="font-size: 0.75rem; font-weight: 400; color: #94a3b8; margin: -10px 0 12px 28px;">(12 months)</p>
                            <div class="chart-wrap"><canvas id="bookingsRevenueChart" width="400" height="200" style="background:#131e2e;padding:12px;border-radius:14px;width:100%;height:100%;display:block;"></canvas></div>
                        </div>
                        <div class="chart-section">
                            <h3 style="margin-bottom:12px;"><i class="fas fa-donut text-purple-500"></i> Status Breakdown</h3>
                            <p style="font-size: 0.75rem; font-weight: 400; color: #94a3b8; margin: -10px 0 12px 28px;">(This Month)</p>
                            <div class="chart-wrap chart-wrap--sm"><canvas id="statusDonut" width="300" height="200" style="background:#131e2e;padding:12px;border-radius:14px;width:100%;height:100%;display:block;"></canvas></div>
                        </div>
                    </div>
                </section>

                <div class="dash-side">
                    <section class="panel">
                    <h3><i class="fas fa-bell text-purple-500"></i> System Alerts</h3>
                    @if (!$lowSeatAlerts)
                        <div class="qa" style="justify-content:flex-start;color:#94a3b8"><i class="fas fa-circle-info"></i><span>No low-seat alerts.</span></div>
                    @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>Departure</th>
                                    <th>Seats Left</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lowSeatAlerts as $al)
                                <tr>
                                    <td>{{ $al['route'] }}</td>
                                    <td>{{ $al['departure'] }}</td>
                                    <td><span class="status-badge status-pending">{!! (int)$al['seats_left'] !!}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </section>
                </div>

                <section class="bus-layout-section">
                        <h3><i class="fas fa-bus text-blue-500"></i> Available Buses ({!! (int)$countBuses !!})</h3>
                        <div class="bus-carousel" id="busCarouselContainer">
                            <button class="carousel-btn prev" onclick="busCarousel.prevBus()"><i class="fas fa-chevron-left"></i></button>
                            <div class="bus-container">
                        @foreach ($busLayouts as $bus)
                        @php($booked_seats_array = $bus["booked_seats"])
                        <div class="bus-card">
                            <div class="seat-layout-header">
                                <h4>{{ $bus['bus_name'] }}</h4>
                                <div class="bus-info">{{ $bus['plate_number'] }}</div>
                                <div class="route-info">{{ $bus['route_name'] }}</div>
                                @if ($bus['trip_id'])
                                <div class="departure-info">Departure: {!! $bus['departure_time'] !!}</div>
                                @else
                                <div class="departure-info" style="color: #9ca3af;">No trip assigned</div>
                                @endif
                                <div style="margin-top: 8px; font-size: 0.9rem; color: #94a3b8;">
                                    @if ($bus['trip_id'])
                                    Seats: {!! count($booked_seats_array) !!}/{!! $bus['capacity'] !!}
                                    @else
                                    Capacity: {!! $bus['capacity'] !!} seats
                                    @endif
                                </div>
                                <div style="margin-top: 8px; display: flex; gap: 8px; justify-content: center;">
                                    @if ($bus['trip_id'])
                                    <span class="status-badge {!! $bus['trip_status'] === 'scheduled' ? 'scheduled' : 'cancelled' !!}">
                                        Trip: {!! ucfirst($bus['trip_status']) !!}
                                    </span>
                                    <span class="status-badge {!! $bus['route_status'] === 'active' ? 'active' : 'cancelled' !!}">
                                        Route: {!! ucfirst($bus['route_status']) !!}
                                    </span>
                                    @else
                                    <span class="status-badge pending">Available for Assignment</span>
                                    @endif
                                </div>
                            </div>
                            <div class="seat-grid">
                                <?php 
                                $totalSeats = $bus['capacity'];
                                $seatsPerRow = 6;
                                $leftSeats = 3;
                                
                                for($row = 0; $row < ceil($totalSeats / $seatsPerRow); $row++) {
                                    // Left side seats
                                    for($col = 0; $col < $leftSeats; $col++) {
                                        $seatNum = $row * $seatsPerRow + $col + 1;
                                        if($seatNum <= $totalSeats) {
                                            if (!$bus['trip_id']) {
                                                // If no trip assigned, show all seats as available
                                                echo '<div class="seat available" data-seat="' . $seatNum . '">S' . $seatNum . '</div>';
                                            } else {
                                                $seatClass = in_array($seatNum, $booked_seats_array) ? 'occupied' : 'available';
                                                echo '<div class="seat ' . $seatClass . '" data-seat="' . $seatNum . '">S' . $seatNum . '</div>';
                                            }
                                        }
                                    }
                                    
                                    // Aisle
                                    echo '<div class="aisle"></div>';
                                    
                                    // Right side seats
                                    for($col = 0; $col < $leftSeats; $col++) {
                                        $seatNum = $row * $seatsPerRow + $col + 4;
                                        if($seatNum <= $totalSeats) {
                                            if (!$bus['trip_id']) {
                                                // If no trip assigned, show all seats as available
                                                echo '<div class="seat available" data-seat="' . $seatNum . '">S' . $seatNum . '</div>';
                                            } else {
                                                $seatClass = in_array($seatNum, $booked_seats_array) ? 'occupied' : 'available';
                                                echo '<div class="seat ' . $seatClass . '" data-seat="' . $seatNum . '">S' . $seatNum . '</div>';
                                            }
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <div class="seat-legend">
                                <div class="legend-item">
                                    <div class="legend-box legend-available"></div>
                                    <span>Available</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-box legend-booked"></div>
                                    <span>Booked</span>
                                </div>
                            </div>
                            <div class="carousel-dots" style="margin-top: 8px;"></div>
                        </div>
                        @endforeach
                        </div>
                        <button class="carousel-btn next" onclick="busCarousel.nextBus()"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </section>

                <section class="panel span-2">
                    <div class="panel-head">
                        <h3><i class="fas fa-calendar-days text-blue-500"></i> Today &amp; Upcoming Trips</h3>
                        <a href="{{ route('admin.trips') }}" class="panel-link">Manage trips <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Route</th>
                                    <th>Departure</th>
                                    <th>Bus</th>
                                    <th>Status</th>
                                    <th>Seats Booked</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($upcomingTrips as $u)
                                <tr>
                                    <td>{{ $u['route'] }}</td>
                                    <td>{{ $u['departure'] }}</td>
                                    <td>{{ $u['bus'] }}</td>
                                    <td><span class="status-badge {{ strtolower($u['status']) }}">{{ ucfirst($u['status']) }}</span></td>
                                    <td>
                                        <div class="seat-fill">
                                            <span class="seat-fill-text">{{ (int) $u['booked'] }}/{{ (int) $u['capacity'] }}</span>
                                            <span class="seat-fill-bar"><span style="width: {{ (int) $u['capacity'] > 0 ? round((int) $u['booked'] / (int) $u['capacity'] * 100) : 0 }}%"></span></span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center">No upcoming trips</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="panel span-3">
                    <div class="panel-head">
                        <h3><i class="fas fa-ticket text-green-500"></i> Recent Bookings</h3>
                        <a href="{{ route('admin.bookings') }}" class="panel-link">All bookings <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Passenger</th>
                                    <th>Route</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!$recentBookings)
                                <tr><td colspan="5" class="text-center">No recent bookings</td></tr>
                                @else
                            @foreach ($recentBookings as $booking)
                                <tr>
                                    <td>{{ $booking['booking_code'] }}</td>
                                    <td>{{ $booking['passenger_name'] }}</td>
                                    <td>{{ $booking['route'] }}</td>
                                    <td>₱{!! number_format($booking['amount'], 2) !!}</td>
                                    <td><span class="status-badge {!! strtolower($booking['status']) !!}">{!! ucfirst($booking['status']) !!}</span></td>
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>
    <script src="{{ asset('assets/js/bus-carousel.js') }}"></script>
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/chartjs-plugin-datalabels.min.js') }}"></script>
    <script src="{{ asset('assets/js/message-handler.js') }}"></script>
    <!-- Load Lottie player locally for offline support -->
    <script src="{{ asset('assets/js/lottie/lottie-player.js') }}"></script>
    <script src="{{ asset('assets/js/weather-integration.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize current time display
        function updateTime() {
            const now = new Date();
            const timeElement = document.getElementById('current-time');
            const dateStr = now.toLocaleDateString('en-US', {
                weekday: 'long',
                month: 'long',
                day: 'numeric'
            });
            const timeStr = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit',
                hour12: true 
            });
            timeElement.textContent = `${dateStr} · ${timeStr}`;
        }
        updateTime();
        setInterval(updateTime, 1000);

        if (typeof weatherDashboard !== 'undefined') {
            weatherDashboard.updateCurrentLocationWeather()
                .catch(error => {
                    console.error('Failed to update weather information:', error);
                });
                
            // Update weather every 30 minutes
            setInterval(() => {
                weatherDashboard.updateCurrentLocationWeather()
                    .catch(error => {
                        console.error('Failed to update weather information:', error);
                    });
            }, 30 * 60 * 1000);
            
            // Real-time internet connectivity detection
            // Detect when internet goes offline
            window.addEventListener('offline', () => {
                console.log('Internet connection lost');
                weatherDashboard.updateCurrentLocationWeather();
            });
            
            // Detect when internet comes back online
            window.addEventListener('online', () => {
                console.log('Internet connection restored, waiting before reconnecting...');
                // Wait 1.5 seconds before attempting to reconnect
                // This gives the network and backend services time to stabilize
                setTimeout(() => {
                    weatherDashboard.updateCurrentLocationWeather();
                }, 1500);
            });
        }
        // Wait for Chart.js to be loaded
        if (typeof Chart === 'undefined') {
            console.error('Chart.js library not loaded');
            return;
        }

        // Register the datalabels plugin
        if (typeof ChartDataLabels !== 'undefined') {
            Chart.register(ChartDataLabels);
        }

        try {
            // Get data from database - months generated entirely from database records
            const labels = {!! json_encode($dayLabels) !!};
            const bookings = {!! json_encode($bookingsData) !!};
            const revenue = {!! json_encode($revenueData) !!};
            const statusData = {!! json_encode(array_values($statusBreakdown)) !!}; // [confirmed, pending, cancelled]

            console.log('Chart data from database (months generated from DB records only):', { labels, bookings, revenue, statusData });

            // Bookings & Revenue Chart
            const ctx1 = document.getElementById('bookingsRevenueChart');
            if (ctx1) {
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        // Month labels generated entirely from database records (only months with data)
                        // Data sourced from database queries above
                        labels: labels || ['No Data'],
                        datasets: [
                            { 
                                label: 'Bookings', 
                                // Data from database: SELECT COUNT(*) FROM bookings WHERE booked_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
                                // Only shows months that have actual database records
                                data: bookings || [0], 
                                backgroundColor: 'rgba(59,130,246,.6)', 
                                borderRadius: 6,
                                borderColor: '#3b82f6',
                                borderWidth: 1
                            },
                            { 
                                label: 'Revenue (₱)',
                                // Data from database: SELECT SUM(amount) FROM bookings WHERE status='confirmed' AND booked_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
                                // Only shows months that have actual database records
                                data: revenue || [0], 
                                type: 'line', 
                                yAxisID: 'y1', 
                                borderColor: '#22c55e', 
                                backgroundColor: 'rgba(34,197,94,.15)', 
                                tension: 0.35, 
                                fill: true,
                                borderWidth: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        resizeDelay: 100,
                        layout: {
                            padding: {
                                left: 10,
                                right: 10,
                                top: 10,
                                bottom: 10
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                ticks: { color: '#9ca3af' },
                                grid: { color: 'rgba(255, 255, 255, 0.1)' }
                            },
                            y1: { 
                                beginAtZero: true, 
                                position: 'right', 
                                grid: { drawOnChartArea: false }, 
                                ticks: { color: '#9ca3af' }
                            },
                            x: {
                                ticks: { color: '#9ca3af' },
                                grid: { color: 'rgba(255,255,255,0.1)' }
                            }
                        },
                        plugins: { 
                            legend: { 
                                labels: { color: '#e5e7eb' },
                                position: 'top'
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                titleColor: '#e5e7eb',
                                bodyColor: '#e5e7eb',
                                borderColor: 'rgba(255,255,255,0.1)',
                                borderWidth: 1
                            }
                        }
                    }
                });
            }

            // Status Donut Chart (Confirmed, Cancelled, Completed, Refunded)
            const ctx2 = document.getElementById('statusDonut');
            if (ctx2) {
                new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        labels: ['Confirmed', 'Cancelled', 'Completed'],
                        datasets: [{
                            // statusData = [confirmed, pending, cancelled, completed, refunded]
                            data: [
                                (statusData && statusData[0]) ? statusData[0] : 0,
                                (statusData && statusData[2]) ? statusData[2] : 0,
                                (statusData && statusData[3]) ? statusData[3] : 0,
                                (statusData && statusData[4]) ? statusData[4] : 0
                            ],
                            backgroundColor: ['#22c55e', '#ef4444', '#17a2b8', '#fb923c'],
                            borderColor: ['#16a34a', '#dc2626', '#0891b2', '#f97316'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        resizeDelay: 100,
                        layout: {
                            padding: {
                                left: 10,
                                right: 10,
                                top: 10,
                                bottom: 10
                            }
                        },
                        onHover: function(event, elements) {
                            // Force legend text color
                            const legend = this.legend;
                            if (legend && legend.legendItems) {
                                legend.legendItems.forEach(item => {
                                    if (item.textElement) {
                                        item.textElement.fillStyle = '#ffffff';
                                    }
                                });
                            }
                        },
                        plugins: { 
                            datalabels: {
                                color: '#ffffff',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                formatter: function(value, context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return value > 0 ? `${context.chart.data.labels[context.dataIndex]}\n${percentage}%` : '';
                                },
                                textAlign: 'center',
                                textStrokeColor: 'rgba(0,0,0,0.8)',
                                textStrokeWidth: 2
                            },
                            legend: { 
                                labels: { 
                                    color: '#ffffff',
                                    font: {
                                        size: 13,
                                        weight: '500'
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            const dataset = data.datasets[0];
                                            const total = dataset.data.reduce((a, b) => a + b, 0);
                                            return data.labels.map((label, i) => {
                                                const value = dataset.data[i];
                                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                                return {
                                                    text: `${label}: ${value} (${percentage}%)`,
                                                    fillStyle: dataset.backgroundColor[i],
                                                    strokeStyle: dataset.borderColor[i],
                                                    lineWidth: dataset.borderWidth,
                                                    hidden: false,
                                                    index: i,
                                                    fontColor: '#ffffff'
                                                };
                                            });
                                        }
                                        return [];
                                    }
                                },
                                position: 'bottom',
                                padding: 15
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.9)',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: 'rgba(255,255,255,0.3)',
                                borderWidth: 1,
                                titleFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 13,
                                    weight: '500'
                                },
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                
                // Force legend text color after chart creation
                setTimeout(() => {
                    const legendItems = ctx2.parentNode.querySelectorAll('.chartjs-legend-text');
                    legendItems.forEach(item => {
                        item.style.color = '#ffffff';
                    });
                }, 100);
            }
        } catch (error) {
            console.error('Error initializing charts:', error);
        }
    });
    </script>
    
    <!-- Logout Confirmation Modal -->
    <div id="logoutConfirmModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header">
                <div class="confirm-modal-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h3 class="confirm-modal-title">Confirm Logout</h3>
            </div>
            <div class="confirm-modal-body">
                <p>Are you sure you want to logout?</p>
                <p style="margin-top: 12px; color: #9ca3af; font-size: 13px;">
                    <i class="fas fa-info-circle"></i> You will need to login again to access your account.
                </p>
            </div>
            <div class="confirm-modal-footer">
                <button type="button" class="confirm-modal-btn confirm-modal-btn-secondary" onclick="hideLogoutConfirmModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="confirm-modal-btn confirm-modal-btn-primary" id="logoutConfirmBtn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('assets/js/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/modal-animations.js') }}"></script>
    <script>
        // Logout confirmation modal
        const logoutLink = document.querySelector('.logout');
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (window.animateModalOpen) {
                    animateModalOpen('#logoutConfirmModal');
                } else {
                    document.getElementById('logoutConfirmModal').style.display = 'block';
                }
            });
        }
        
        function hideLogoutConfirmModal() {
            if (window.animateModalClose) {
                animateModalClose('#logoutConfirmModal');
            } else {
                document.getElementById('logoutConfirmModal').style.display = 'none';
            }
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('logoutConfirmModal');
            if (event.target === modal) {
                hideLogoutConfirmModal();
            }
        });
    </script>
</body>
</html>

