<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/csrf.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings - Bus Reservation System</title>
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
    <link href="{{ asset('assets/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/fonts/poppins.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/message-handler.css') }}">
    <style>
    /* Receipt Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        backdrop-filter: blur(4px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease-out;
        visibility: hidden;
    }

    .modal-overlay.is-visible {
        display: block;
        opacity: 1;
        pointer-events: auto;
        visibility: visible;
    }

    .modal-overlay.is-hiding {
        opacity: 0;
        pointer-events: none;
        visibility: hidden;
    }

    .modal-container {
        position: fixed;
        top: 50%;
        left: 50%;
        background: #1a222c;
        border-radius: 12px;
        padding: 24px;
        max-width: 500px;
        width: 90%;
        z-index: 1001;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        max-height: 90vh;
        overflow-y: auto;
        scrollbar-color: rgba(59, 130, 246, 0.3) transparent;
        scrollbar-width: thin;
    }

    /* WebKit scrollbar styles for receipt modal container */
    .modal-container::-webkit-scrollbar {
        width: 6px;
    }

    .modal-container::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 10px;
    }

    .modal-container::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.35);
        border-radius: 10px;
        border: none;
        min-height: 40px;
    }

    .modal-container::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.55);
    }

    .modal-overlay.is-visible .modal-container {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .modal-overlay.is-hiding .modal-container {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes modalSlideInScale {
        from {
            opacity: 0;
            transform: translate(-50%, -60%) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
    }
    .modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        font-size: 20px;
        padding: 4px;
        transition: color 0.2s ease;
    }
    .modal-close:hover {
        color: #ef4444;
    }
    .receipt-content {
        font-family: 'Courier New', monospace;
        color: #e5e7eb;
    }
    .receipt-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 1px dashed #4b5563;
        padding-bottom: 16px;
    }
    .receipt-header h2 {
        color: #3b82f6;
        margin: 0;
    }
    .receipt-details {
        margin: 20px 0;
    }
    .receipt-details p {
        margin: 8px 0;
        display: flex;
        justify-content: space-between;
    }
    .receipt-footer {
        text-align: center;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px dashed #4b5563;
        font-size: 14px;
        color: #9ca3af;
    }
    .receipt-actions {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 24px;
    }
    .receipt-actions button {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .print-btn {
        background: #3b82f6;
        color: white;
    }
    .print-btn:hover {
        background: #2563eb;
    }
    .close-btn {
        background: #4b5563;
        color: white;
    }
    .close-btn:hover {
        background: #374151;
    }
    @media print {
        body * {
            visibility: hidden;
        }
        .modal-container * {
            visibility: visible;
        }
        .modal-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            transform: none;
            background: white !important;
            color: black !important;
        }
        .modal-close, .receipt-actions {
            display: none !important;
        }
        .receipt-content {
            color: black !important;
        }
        .receipt-header h2 {
            color: black !important;
        }
    }
    
    /* Cancel button styling */
    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        border: 1px solid rgba(239, 68, 68, 0.6);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    .btn-danger i {
        margin-right: 4px;
    }
    
    /* Cancel confirmation modal */
    .cancel-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        backdrop-filter: blur(4px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease-out;
    }

    .cancel-modal.is-visible {
        opacity: 1;
        pointer-events: auto;
    }

    .cancel-modal.is-hiding {
        opacity: 0;
        pointer-events: none;
    }
    
    .cancel-modal-content {
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
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .cancel-modal.is-visible .cancel-modal-content {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    
    .cancel-modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    
    .cancel-modal-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(239, 68, 68, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ef4444;
        font-size: 18px;
    }
    
    .cancel-modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #e5e7eb;
        margin: 0;
    }
    
    .cancel-modal-body {
        color: #9ca3af;
        margin-bottom: 20px;
        line-height: 1.5;
    }
    
    .cancel-modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }
    
    .cancel-modal-btn {
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
    
    .cancel-modal-btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: #e5e7eb;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .cancel-modal-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.15);
    }
    
    .cancel-modal-btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        border: 1px solid rgba(239, 68, 68, 0.6);
    }
    
    .cancel-modal-btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    /* Confirm modal styles - matching admin bookings */
    .confirm-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1002;
        backdrop-filter: blur(4px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease-out;
    }
    
    .confirm-modal.is-visible {
        opacity: 1;
        pointer-events: auto;
    }
    
    .confirm-modal.is-hiding {
        opacity: 0;
        pointer-events: none;
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
        z-index: 1003;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .confirm-modal.is-visible .confirm-modal-content {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
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
        display: inline-flex;
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
        text-decoration: none;
    }
    
    .confirm-modal-btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    /* Bus seat layout styles - matching dashboard */
    .seat-layout {
        background: rgba(255,255,255,0.02);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .seat-layout-header {
        text-align: center;
        margin-bottom: 16px;
        color: #e5e7eb;
    }
    .seat-layout-header h4 {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0 0 6px 0;
        color: #3b82f6;
    }
    .seat-layout-header .bus-info {
        font-size: 0.75rem;
        color: #9ca3af;
        margin: 2px 0;
    }
    .seat-layout-header .route-info {
        font-size: 0.9rem;
        font-weight: 500;
        margin: 6px 0;
    }
    .seat-layout-header .departure-info {
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
    .aisle {
        background: transparent;
        pointer-events: none;
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
    .seat:hover {
        background: rgba(59,130,246,0.1);
        border-color: rgba(59,130,246,0.3);
        transform: scale(1.05);
    }
    .seat.selected {
        background: rgba(59,130,246,0.2);
        border-color: rgba(59,130,246,0.6);
        color: #3b82f6;
        transform: scale(1.05);
    }
    .seat.booked {
        background: rgba(239,68,68,0.1);
        border-color: rgba(239,68,68,0.3);
        color: #ef4444;
        cursor: not-allowed;
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
    .legend-available { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); }
    .legend-selected { background: rgba(59,130,246,0.2); border: 1px solid rgba(59,130,246,0.6); }
    .legend-booked { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); }

    /* Two column layout styles */
    .booking-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 1200px) {
        .booking-container {
            grid-template-columns: 1fr;
        }
    }
    
    /* Scoped booking form styles */
    .booking-form { 
        display: grid; 
        grid-template-columns: 1fr; 
        gap: 16px; 
    }
    
    /* Comprehensive Responsive Design */
    @media (max-width: 992px) {
        .booking-form {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .field {
            grid-column: 1 / -1;
        }
        
        .table {
            font-size: 13px;
        }
        
        .table th,
        .table td {
            padding: 8px;
        }
        
        .status-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
    }
    
    @media (max-width: 768px) {
        .booking-form {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .field {
            grid-column: 1 / -1;
        }
        
        .input-container {
            font-size: 14px;
        }
        
        .input-container input,
        .input-container select {
            padding: 10px 12px;
        }
        
        .table {
            font-size: 12px;
        }
        
        .table th,
        .table td {
            padding: 6px;
        }
        
        .panel h3 {
            font-size: 1rem;
        }
        
        .status-badge {
            font-size: 0.65rem;
            padding: 0.15rem 0.35rem;
        }
        
        .btn {
            padding: 8px 12px;
            font-size: 13px;
        }
        
        .modal-container {
            margin: 10px;
            max-width: calc(100vw - 20px);
        }
    }
    
    @media (max-width: 576px) {
        .booking-form {
            gap: 0.5rem;
        }
        
        .input-container {
            font-size: 13px;
        }
        
        .input-container input,
        .input-container select {
            padding: 8px 10px;
        }
        
        .table {
            font-size: 11px;
        }
        
        .table th,
        .table td {
            padding: 4px;
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
            padding: 6px 10px;
            font-size: 12px;
        }
        
        .modal-container {
            margin: 5px;
            max-width: calc(100vw - 10px);
            padding: 1rem;
        }
        
        .receipt-content {
            font-size: 12px;
        }
        
        .receipt-actions {
            flex-direction: column;
            gap: 8px;
        }
        
        .receipt-actions button {
            width: 100%;
            justify-content: center;
        }
    }
    
    @media (max-width: 480px) {
        .booking-form {
            gap: 0.4rem;
        }
        
        .input-container {
            font-size: 12px;
        }
        
        .input-container input,
        .input-container select {
            padding: 6px 8px;
        }
        
        .table {
            font-size: 10px;
        }
        
        .table th,
        .table td {
            padding: 3px;
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
            padding: 0.1rem 0.25rem;
        }
        
        .btn {
            padding: 5px 8px;
            font-size: 11px;
        }
        
        .modal-container {
            margin: 2px;
            max-width: calc(100vw - 4px);
            padding: 0.75rem;
        }
        
        .receipt-content {
            font-size: 11px;
        }
    }
    
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label { font-size: 14px; color: #e5e7eb; font-weight: 500; }
    .input-container {
        position: relative; display: flex; align-items: center;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 6px; padding: 10px 12px;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .input-container:focus-within {
        border-color: #3b82f6;
        background: rgba(255,255,255,0.08);
    }
    .input-icon { color: #9ca3af; margin-right: 10px; font-size: 14px; }
    .input-container input,
    .input-container select {
        flex: 1; background: transparent; border: none; outline: none;
        color: #e5e7eb; font-size: 14px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .input-container select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 30px;
    }
    .input-container select option {
        background-color: #1a222c;
        color: #e5e7eb;
        padding: 8px;
    }
    .input-container select:focus option:checked {
        background: #3b82f6;
        color: white;
    }
    .input-container select option:hover {
        background-color: #2a3441;
    }
    .input-focus-line {
        position: absolute; left: 14px; right: 14px; bottom: 6px; height: 2px;
        background: linear-gradient(90deg, transparent, rgba(59,130,246,0.7), transparent);
        opacity: 0; transform: scaleX(0.6);
        transition: transform .2s ease, opacity .2s ease;
    }
    .input-container:focus-within .input-focus-line { opacity: 1; transform: scaleX(1); }
    .booking-actions { grid-column: 1 / -1; display: flex; justify-content: flex-end; align-items: end; }
    .booking-actions .btn { width: auto; }
    
    /* Cancel button styling */
    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        border: 1px solid rgba(239, 68, 68, 0.6);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    .btn-danger i {
        margin-right: 4px;
    }
    
    /* Booking Form Modal Styles */
    .booking-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        backdrop-filter: blur(4px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease-out;
        visibility: hidden;
    }

    .booking-modal.is-visible {
        opacity: 1;
        pointer-events: auto;
        visibility: visible;
    }

    .booking-modal.is-hiding {
        opacity: 0;
        pointer-events: none;
        visibility: hidden;
    }

    .booking-modal-container {
        position: fixed;
        top: 50%;
        left: 50%;
        background: #1a222c;
        border-radius: 12px;
        padding: 24px;
        max-width: 900px;
        width: 95%;
        max-height: 90vh;
        overflow-y: auto;
        z-index: 1001;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        scrollbar-color: rgba(59, 130, 246, 0.3) transparent;
        scrollbar-width: thin;
    }

    /* WebKit scrollbar styles for booking modal container */
    .booking-modal-container::-webkit-scrollbar {
        width: 6px;
    }

    .booking-modal-container::-webkit-scrollbar-track {
        background: transparent;
        border-radius: 10px;
    }

    .booking-modal-container::-webkit-scrollbar-thumb {
        background: rgba(59, 130, 246, 0.35);
        border-radius: 10px;
        border: none;
        min-height: 40px;
    }

    .booking-modal-container::-webkit-scrollbar-thumb:hover {
        background: rgba(59, 130, 246, 0.55);
    }

    .booking-modal.is-visible .booking-modal-container {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .booking-modal.is-hiding .booking-modal-container {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
    }

    .booking-modal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .booking-modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #e5e7eb;
        margin: 0;
    }

    .booking-modal-close {
        margin-left: auto;
        background: none;
        border: none;
        color: #9ca3af;
        font-size: 22px;
        cursor: pointer;
        padding: 4px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .booking-modal-close:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #e5e7eb;
    }

    .booking-modal-icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
        font-size: 18px;
        flex-shrink: 0;
    }

    .booking-modal-subtitle {
        font-size: 13px;
        color: #9ca3af;
        margin: 4px 0 0 0;
    }

    .booking-modal-body {
        color: #9ca3af;
        margin-bottom: 20px;
    }

    .booking-modal-footer {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .booking-modal-footer .btn {
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

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: 1px solid rgba(59, 130, 246, 0.6);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-outline {
        background: rgba(255, 255, 255, 0.1);
        color: #e5e7eb;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .booking-modal-content {
        color: #e5e7eb;
    }

    .booking-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 20px;
        margin-bottom: 16px;
    }

    @media (max-width: 640px) {
        .booking-form-row {
            grid-template-columns: 1fr;
        }
    }

    .create-booking-btn {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: 1px solid rgba(59, 130, 246, 0.6);
        border-radius: 6px;
        cursor: pointer;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .create-booking-btn:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    @media (max-width: 1024px) {
        .booking-modal-container {
            max-width: 95vw;
            max-height: 95vh;
            padding: 20px;
        }
    }

    @media (max-width: 768px) {
        .booking-modal-container {
            max-width: 95vw;
            max-height: 95vh;
            padding: 16px;
        }

        .booking-modal-title {
            font-size: 18px;
        }

        .booking-container {
            grid-template-columns: 1fr !important;
        }
    }
    .autocomplete-container {
        position: relative;
    }
    .autocomplete-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #2a3441;
        border: 1px solid rgba(59,130,246,0.3);
        border-radius: 8px;
        margin-top: 4px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
    }
    .autocomplete-item {
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        transition: all 0.2s ease;
    }
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    .autocomplete-item:hover {
        background: rgba(59,130,246,0.1);
    }
    .autocomplete-name {
        color: #e5e7eb;
        font-size: 14px;
        font-weight: 500;
    }
    .autocomplete-phone {
        color: #9ca3af;
        font-size: 12px;
        margin-top: 2px;
    }

    /* Logout confirmation modal styles - matching other modals */
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
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease-out;
    }
    
    .confirm-modal.is-visible {
        opacity: 1;
        pointer-events: auto;
    }
    
    .confirm-modal.is-hiding {
        opacity: 0;
        pointer-events: none;
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
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .confirm-modal.is-visible .confirm-modal-content {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
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
        display: inline-flex;
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
        text-decoration: none;
    }
    
    .confirm-modal-btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    
    /* Status Badge Styling */
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
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/mobile.css') }}">
    <script src="{{ asset('assets/js/mobile-responsive.js') }}" defer></script>
</head>
<body>
    <div class="dash-wrap">
        @include('partials.sidebar')
        <button class="btn-ghost hamburger-btn" id="mobileMenuToggle" title="Toggle menu"><i class="fas fa-bars"></i></button>
        <main class="content">
            <header class="topbar">
                <div class="welcome"><h2>Bookings</h2><p>Manage and review reservations</p></div>
                <div class="actions"></div>
            </header>
            <section class="panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;">My Bookings</h3>
                    <button type="button" class="create-booking-btn" onclick="openBookingModal()"><i class="fas fa-plus"></i> Create Booking</button>
                </div>
                @if ($ok)<div class="qa" style="justify-content:flex-start;color:#22c55e"><i class="fas fa-check-circle"></i><span>{!! $ok !!}</span></div>@endif
                @if ($formErrors)<div class="qa" style="justify-content:flex-start;color:#ef4444"><i class="fas fa-circle-exclamation"></i><span>{{ $formErrors['general'] ?? reset($formErrors) }}</span></div>@endif
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Passenger</th>
                                <th>Route</th>
                                <th>Departure</th>
                                <th>Seat</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Booked</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!$bookings)
                            <tr><td colspan="9" style="text-align:center">No bookings yet.</td></tr>
                            @else
                            @foreach ($bookings as $b)
                            <tr>
                                <td>{{ $b['booking_code'] }}</td>
                                <td>{{ $b['passenger_name'] }}</td>
                                <td>{{ $b['route_name'] }}</td>
                                <td>{{ $b['dep_time'] }}</td>
                                <td>{!! (int)$b['seat_number'] !!}</td>
                                <td>{!! number_format((float)$b['amount'],2) !!}</td>
                                <td><span class="status-badge {{ $b['status'] }}">{!! ucfirst(htmlspecialchars($b['status'])) !!}</span></td>
                                <td>{{ $b['booked_at'] }}</td>
                                <td>
                                    <?php 
                                    // Show cancel button only for confirmed/pending bookings that haven't departed
                                    $can_cancel = in_array($b['status'], ['confirmed', 'pending']) && 
                                                strtotime($b['dep_time']) > time();
                                    if ($can_cancel): ?>
                                    <button type="button" class="btn-danger" style="padding: 4px 8px; font-size: 12px;" 
                                            onclick="showCancelModal({!! (int)$b['id'] !!}, '{{ $b['booking_code'] }}', '{{ $b['route_name'] }}', '{{ $b['dep_time'] }}')">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                    @else
                                    <span style="color: #9ca3af; font-size: 12px;">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="modal-overlay">
        <div class="modal-container">
            <button class="modal-close" onclick="closeReceiptModal()"><i class="fas fa-times"></i></button>
            <div id="receiptContent" class="receipt-content"></div>
        </div>
    </div>

    <!-- Create Booking Modal -->
    <div id="bookingFormModal" class="booking-modal">
        <div class="booking-modal-container">
            <div class="booking-modal-header">
                <div class="booking-modal-icon-wrapper">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                    <h2 class="booking-modal-title">Create Booking</h2>
                    <p class="booking-modal-subtitle">Add a new booking</p>
                </div>
                <button type="button" class="booking-modal-close" onclick="closeBookingModal()"><i class="fas fa-times"></i></button>
            </div>

            <form method="post" action="">
    @csrf
                <div class="booking-modal-content">
                    <div class="booking-container">
                        <!-- Left Column: Booking Form -->
                        <div>
                            <div class="field">
                                <label for="trip_id"><i class="fas fa-route"></i> Select Trip</label>
                                <div class="input-container">
                                    <select name="trip_id" id="trip_id" required>
                                        <option value="">Select Trip</option>
                                        @foreach ($trips as $t)
                                        <option value="{!! (int)$t['id'] !!}">{{ $t['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="booking-form-row">
                                <div class="field">
                                    <label for="seat_number"><i class="fas fa-chair"></i> Seat #</label>
                                    <div class="input-container">
                                        <input type="number" id="seat_number" name="seat_number" placeholder="Seat #" min="1" required readonly>
                                    </div>
                                </div>

                                <div class="field">
                                    <label for="amount"><i class="fas fa-peso-sign"></i> Amount (Required: <span id="ticket-price">₱0.00</span>)</label>
                                    <div class="input-container">
                                        <input type="number" id="amount" name="amount" placeholder="Amount" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <small class="price-warning" style="color: #ef4444; display: none;">Amount must be equal to or greater than the ticket price</small>
                            <small id="seatError" style="color: #ef4444; display: none;">Please select a seat first</small>
                        </div>

                        <!-- Right Column: Seat Selection -->
                        <div class="seat-layout" id="seatLayoutContainer" style="display: none;">
                            <div class="seat-layout-header">
                                <h4>TOWNLINK EXPRESS</h4>
                                <p id="route-display">Select a trip to view available seats</p>
                            </div>
                            <div id="seatGrid" class="seat-grid">
                                <!-- Seats will be generated dynamically via JavaScript based on bus capacity -->
                            </div>
                            <div class="seat-legend">
                                <div class="legend-item">
                                    <div class="legend-box legend-available"></div>
                                    <span>Available</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-box legend-selected"></div>
                                    <span>Selected</span>
                                </div>
                                <div class="legend-item">
                                    <div class="legend-box legend-booked"></div>
                                    <span>Booked</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="booking-modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeBookingModal()"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-check"></i> Create Booking</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Booking Confirmation Modal -->
    <div id="bookingConfirmModal" class="confirm-modal">
        <div class="confirm-modal-content">
            <div class="confirm-modal-header">
                <div class="confirm-modal-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="confirm-modal-title">Confirm Booking</h3>
            </div>
            <div class="confirm-modal-body">
                <p>Please review your booking details before confirming:</p>
                <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; margin-top: 12px;">
                    <div><strong>Route:</strong> <span id="confirmRoute"></span></div>
                    <div><strong>Departure:</strong> <span id="confirmDeparture"></span></div>
                    <div><strong>Seat Number:</strong> <span id="confirmSeat"></span></div>
                    <div id="discountInfoSection" style="display: none; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 8px; margin-top: 8px;">
                        <div><strong>Original Price:</strong> <span id="confirmOriginalPrice"></span></div>
                        <div><strong>Discount:</strong> <span id="confirmDiscount" style="color: #22c55e;"></span></div>
                    </div>
                    <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 8px; margin-top: 8px;"><strong>Amount to Pay (Ticket Price):</strong> <span id="confirmAmount" style="color: #3b82f6; font-weight: 600;"></span></div>
                    <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 8px; margin-top: 8px;"><strong>Amount Paid by Customer:</strong> <span id="confirmAmountPaid" style="color: #e5e7eb;"></span></div>
                    <div id="changeInfoSection" style="display: none; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 8px; margin-top: 8px;">
                        <div><strong>Change:</strong> <span id="confirmChange" style="color: #22c55e; font-weight: 600;"></span></div>
                    </div>
                </div>
            </div>
            <div class="confirm-modal-footer">
                <button type="button" class="confirm-modal-btn confirm-modal-btn-secondary" onclick="hideBookingConfirmModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="confirm-modal-btn confirm-modal-btn-primary" id="confirmBookingBtn">
                    <i class="fas fa-check"></i> Confirm Booking
                </button>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div id="cancelModal" class="cancel-modal">
        <div class="cancel-modal-content">
            <div class="cancel-modal-header">
                <div class="cancel-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="cancel-modal-title">Cancel Booking</h3>
            </div>
            <div class="cancel-modal-body">
                <p>Are you sure you want to cancel this booking?</p>
                <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; margin-top: 12px;">
                    <div><strong>Booking Code:</strong> <span id="modalBookingCode"></span></div>
                    <div><strong>Route:</strong> <span id="modalRoute"></span></div>
                    <div><strong>Departure:</strong> <span id="modalDeparture"></span></div>
                </div>
                <p style="margin-top: 12px; color: #ef4444; font-size: 13px;">
                    <i class="fas fa-info-circle"></i> This action cannot be undone.
                </p>
            </div>
            <div class="cancel-modal-footer">
                <button type="button" class="cancel-modal-btn cancel-modal-btn-secondary" onclick="closeCancelModal()">
                    <i class="fas fa-times"></i> Keep Booking
                </button>
                <form method="post" style="display: inline;">
    @csrf
                    <input type="hidden" name="booking_id" id="modalBookingId">
                    <button type="submit" name="cancel_booking" class="cancel-modal-btn cancel-modal-btn-danger">
                        <i class="fas fa-ban"></i> Cancel Booking
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/sidebar.js') }}"></script>
    <script src="{{ asset('assets/js/modal-animations.js') }}"></script>
    <script src="{{ asset('assets/js/message-handler.js') }}"></script>
    <script>
        // Booking Modal Functions
        function openBookingModal() {
            const modal = document.getElementById('bookingFormModal');
            if (window.animateModalOpen) {
                animateModalOpen(modal);
            } else {
                modal.style.display = 'block';
                modal.classList.add('is-visible');
            }
        }

        function closeBookingModal() {
            const modal = document.getElementById('bookingFormModal');
            if (window.animateModalClose) {
                animateModalClose(modal);
            } else {
                modal.classList.remove('is-visible');
                modal.classList.add('is-hiding');
                setTimeout(() => {
                    modal.style.display = 'none';
                    modal.classList.remove('is-hiding', 'is-visible');
                }, 300);
            }
        }

        // Close modal when clicking outside
        document.getElementById('bookingFormModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeBookingModal();
            }
        });
    </script>
    <script>
        // Store trip data for easy access
        const tripData = {!! json_encode($trips) !!};
        
        // Store current user type for discount calculation
        let currentUserType = '{!! $user_type !!}';
        
        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(amount);
        }
        
        // Calculate discount based on user type
        function calculateDiscount(basePrice, userType) {
            const discountPercent = (userType === 'Student' || userType === 'Senior Citizen/PWD') ? 20 : 0;
            const discountAmount = (basePrice * discountPercent) / 100;
            const discountedPrice = basePrice - discountAmount;
            
            return {
                original_price: basePrice,
                discounted_price: discountedPrice,
                discount_percent: discountPercent,
                discount_amount: discountAmount
            };
        }
        
        // Generate receipt content
        function generateReceipt(bookingData) {
            // Calculate discounted price
            const discountedPrice = bookingData.original_price - bookingData.discount_amount;
            const amountPaid = parseFloat(bookingData.amount);
            const change = amountPaid - discountedPrice;
            
            // Build discount section if applicable
            let discountSection = '';
            if (bookingData.discount_percent > 0) {
                discountSection = `
                    <p><span>Original Price:</span> <span>${formatCurrency(bookingData.original_price)}</span></p>
                    <p><span>Discount (${bookingData.discount_percent}% - ${bookingData.user_type}):</span> <span style="color: #22c55e;">-${formatCurrency(bookingData.discount_amount)}</span></p>
                    <p style="border-top: 1px dashed #4b5563; padding-top: 8px; margin-top: 8px;"><span>Ticket Price to Pay:</span> <span style="font-weight: 600;">${formatCurrency(discountedPrice)}</span></p>
                `;
            } else {
                discountSection = `
                    <p><span>Ticket Price to Pay:</span> <span style="font-weight: 600;">${formatCurrency(bookingData.original_price)}</span></p>
                `;
            }

            // Build change section if applicable
            let changeSection = '';
            if (change > 0) {
                changeSection = `
                    <p style="border-top: 1px dashed #4b5563; padding-top: 8px; margin-top: 8px;"><span>Change:</span> <span style="color: #22c55e; font-weight: 600;">${formatCurrency(change)}</span></p>
                `;
            }
            
            const receipt = `
                <div class="receipt-header">
                    <h2>BusReserve</h2>
                    <p>Travel Receipt</p>
                    <p>Booking #: ${bookingData.booking_code}</p>
                </div>
                
                <div class="receipt-details">
                    <p><span>Route:</span> <span>${bookingData.route_name}</span></p>
                    <p><span>Date:</span> <span>${bookingData.dep_time}</span></p>
                    <p><span>Passenger:</span> <span>${bookingData.passenger_name}</span></p>
                    <p><span>Seat Number:</span> <span>${bookingData.seat_number}</span></p>
                    ${discountSection}
                    <p><span>Amount Paid by Customer:</span> <span style="color: #3b82f6; font-weight: 600;">${formatCurrency(amountPaid)}</span></p>
                    ${changeSection}
                </div>
                
                <div class="receipt-footer">
                    <p>Thank you for choosing BusReserve!</p>
                    <p>Keep this receipt for your records.</p>
                </div>

                <div class="receipt-actions">
                    <button class="print-btn" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                    <button class="close-btn" onclick="closeReceiptModal()">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            `;
            
            // Show the modal with receipt content
            const modal = document.getElementById('receiptModal');
            const receiptContent = document.getElementById('receiptContent');
            receiptContent.innerHTML = receipt;
            if (window.animateModalOpen) {
                animateModalOpen(modal);
            } else {
                modal.style.display = 'block';
                modal.classList.add('is-visible');
            }
        };

        // Close receipt modal
        function closeReceiptModal() {
            const modal = document.getElementById('receiptModal');
            if (window.animateModalClose) {
                animateModalClose(modal);
            } else {
                modal.classList.remove('is-visible');
                modal.classList.add('is-hiding');
                setTimeout(() => {
                    modal.style.display = 'none';
                    modal.classList.remove('is-hiding', 'is-visible');
                }, 300);
            }
        }

        // Close modal when clicking outside
        document.getElementById('receiptModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeReceiptModal();
            }
        });

        // Cancel modal functions
        function showCancelModal(bookingId, bookingCode, route, departure) {
            document.getElementById('modalBookingId').value = bookingId;
            document.getElementById('modalBookingCode').textContent = bookingCode;
            document.getElementById('modalRoute').textContent = route;
            document.getElementById('modalDeparture').textContent = departure;
            if (window.animateModalOpen) {
                animateModalOpen(document.getElementById('cancelModal'));
            } else {
                document.getElementById('cancelModal').style.display = 'block';
            }
        }

        function closeCancelModal() {
            if (window.animateModalClose) {
                animateModalClose(document.getElementById('cancelModal'));
            } else {
                document.getElementById('cancelModal').style.display = 'none';
            }
        }

        // Close cancel modal when clicking outside
        window.addEventListener('click', function(event) {
            const cancelModal = document.getElementById('cancelModal');
            if (event.target === cancelModal) {
                closeCancelModal();
            }
        });
        
        // Booking Confirmation Modal Functions - GLOBAL SCOPE
        function hideBookingConfirmModal() {
            if (window.animateModalClose) {
                animateModalClose(document.getElementById('bookingConfirmModal'));
            } else {
                document.getElementById('bookingConfirmModal').style.display = 'none';
            }
        }

        function showBookingConfirmModal() {
            if (window.animateModalOpen) {
                animateModalOpen(document.getElementById('bookingConfirmModal'));
            } else {
                document.getElementById('bookingConfirmModal').style.display = 'block';
                document.getElementById('bookingConfirmModal').classList.add('is-visible');
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const tripSelect = document.getElementById('trip_id');
            const seatInput = document.getElementById('seat_number');
            const routeDisplay = document.getElementById('route-display');
            const seats = document.querySelectorAll('.seat');
            const amountInput = document.getElementById('amount');
            const ticketPriceDisplay = document.getElementById('ticket-price');
            const priceWarning = document.querySelector('.price-warning');
            let currentTripPrice = 0;
            
            // Seat selection is now handled in the seat generation code

            // Handle trip selection
            tripSelect.addEventListener('change', async function() {
                const seatLayoutContainer = document.getElementById('seatLayoutContainer');
                if (!this.value) {
                    routeDisplay.textContent = 'Select a trip to view available seats';
                    seats.forEach(seat => {
                        seat.classList.remove('booked', 'selected');
                    });
                    seatLayoutContainer.style.display = 'none';
                    return;
                }
                
                // Show seat layout
                seatLayoutContainer.style.display = 'block';

                // Update bus and route display
                const selectedTrip = tripData.find(trip => trip.id === this.value);
                currentTripPrice = parseFloat(selectedTrip.trip_price || selectedTrip.base_price);
                
                // Calculate discount based on current user type
                const discountInfo = calculateDiscount(currentTripPrice, currentUserType);
                if (discountInfo.discount_percent > 0) {
                    ticketPriceDisplay.innerHTML = `₱${discountInfo.discounted_price.toFixed(2)} <span style="font-size: 0.8em; color: #22c55e;">(${discountInfo.discount_percent}% OFF)</span>`;
                } else {
                    ticketPriceDisplay.textContent = `₱${currentTripPrice.toFixed(2)}`;
                }
                ticketPriceDisplay.style.color = ''; // Reset color
                amountInput.value = ''; // Clear amount input
                priceWarning.style.display = 'none'; // Hide warning
                if (selectedTrip) {
                    const header = document.querySelector('.seat-layout-header');
                    header.innerHTML = `
                        <h4>${selectedTrip.bus_name}</h4>
                        <p class="bus-info">Bus Plate: ${selectedTrip.plate_number}</p>
                        <p class="route-info">${selectedTrip.route_name}</p>
                        <p class="departure-info">Departure: ${selectedTrip.departure_time}</p>
                    `;

                                        // Generate seats based on bus capacity (matching dashboard layout)
                    const seatGrid = document.getElementById('seatGrid');
                    seatGrid.innerHTML = '';
                    const seatsPerRow = 6;
                    const leftSeats = 3;
                    const totalSeats = selectedTrip.capacity;
                    
                    for (let row = 0; row < Math.ceil(totalSeats / seatsPerRow); row++) {
                        // Left side seats
                        for (let col = 0; col < leftSeats; col++) {
                            const seatNum = row * seatsPerRow + col + 1;
                            if (seatNum <= totalSeats) {
                                const seatDiv = document.createElement('div');
                                seatDiv.className = 'seat';
                                seatDiv.dataset.seat = seatNum;
                                seatDiv.textContent = seatNum;
                                
                                // Add click handler for seat selection
                                seatDiv.addEventListener('click', function() {
                                    if (this.classList.contains('booked')) return;
                                    
                                    // Toggle selection
                                    const isSelected = this.classList.contains('selected');
                                    
                                    // Clear previous selection
                                    document.querySelectorAll('.seat').forEach(s => s.classList.remove('selected'));
                                    
                                    if (!isSelected) {
                                        // Select this seat if it wasn't selected
                                        this.classList.add('selected');
                                        seatInput.value = this.dataset.seat;
                                    } else {
                                        // Deselect if it was already selected
                                        seatInput.value = '';
                                    }
                                });
                                
                                seatGrid.appendChild(seatDiv);
                            }
                        }
                        
                        // Aisle
                        const aisle = document.createElement('div');
                        aisle.className = 'aisle';
                        seatGrid.appendChild(aisle);
                        
                        // Right side seats
                        for (let col = 0; col < leftSeats; col++) {
                            const seatNum = row * seatsPerRow + col + 4;
                            if (seatNum <= totalSeats) {
                                const seatDiv = document.createElement('div');
                                seatDiv.className = 'seat';
                                seatDiv.dataset.seat = seatNum;
                                seatDiv.textContent = seatNum;
                                
                                // Add click handler for seat selection
                                seatDiv.addEventListener('click', function() {
                                    if (this.classList.contains('booked')) return;
                                    
                                    // Toggle selection
                                    const isSelected = this.classList.contains('selected');
                                    
                                    // Clear previous selection
                                    document.querySelectorAll('.seat').forEach(s => s.classList.remove('selected'));
                                    
                                    if (!isSelected) {
                                        // Select this seat if it wasn't selected
                                        this.classList.add('selected');
                                        seatInput.value = this.dataset.seat;
                                    } else {
                                        // Deselect if it was already selected
                                        seatInput.value = '';
                                    }
                                });
                                
                                seatGrid.appendChild(seatDiv);
                            }
                        }
                    }
                }

                // Clear previous selections
                seats.forEach(seat => {
                    seat.classList.remove('booked', 'selected');
                });
                seatInput.value = '';

                try {
                    // Fetch booked seats for this trip
                    const response = await fetch(`{{ route('api.check-seats') }}?trip_id=${this.value}`);
                    const bookedSeats = await response.json();
                    
                    // Mark booked seats
                    bookedSeats.forEach(seatNum => {
                        const seat = document.querySelector(`.seat[data-seat="${seatNum}"]`);
                        if (seat) seat.classList.add('booked');
                    });
                } catch (error) {
                    console.error('Failed to fetch booked seats:', error);
                }
            });

            // Make seat input readonly since we're using visual selection
            seatInput.readOnly = true;

            // Handle amount validation
            amountInput.addEventListener('input', function() {
                const enteredAmount = this.value ? parseFloat(this.value) : null;
                const ticketPrice = document.getElementById('ticket-price');
                
                // Calculate required amount based on discount
                const discountInfo = calculateDiscount(currentTripPrice, currentUserType);
                const requiredAmount = discountInfo.discounted_price;
                
                // Reset display if input is empty
                if (this.value === '') {
                    priceWarning.style.display = 'none';
                    ticketPrice.style.color = '';
                    if (discountInfo.discount_percent > 0) {
                        ticketPrice.innerHTML = `₱${discountInfo.discounted_price.toFixed(2)} <span style="font-size: 0.8em; color: #22c55e;">(${discountInfo.discount_percent}% OFF)</span>`;
                    } else {
                        ticketPrice.textContent = `₱${currentTripPrice.toFixed(2)}`;
                    }
                    this.setCustomValidity('');
                    return;
                }
                
                if (enteredAmount < requiredAmount) {
                    priceWarning.style.display = 'block';
                    ticketPrice.style.color = '#ef4444'; // Red color for insufficient amount
                    this.setCustomValidity('Amount must be equal to or greater than the ticket price');
                } else {
                    priceWarning.style.display = 'none';
                    ticketPrice.style.color = '#22c55e'; // Green color for sufficient amount
                    if (enteredAmount > requiredAmount) {
                        const change = enteredAmount - requiredAmount;
                        if (discountInfo.discount_percent > 0) {
                            ticketPrice.innerHTML = `<i class="fas fa-check"></i> ₱${discountInfo.discounted_price.toFixed(2)} (Change: ₱${change.toFixed(2)})`;
                        } else {
                            ticketPrice.innerHTML = `<i class="fas fa-check"></i> ₱${currentTripPrice.toFixed(2)} (Change: ₱${change.toFixed(2)})`;
                        }
                    } else {
                        if (discountInfo.discount_percent > 0) {
                            ticketPrice.innerHTML = `<i class="fas fa-check"></i> ₱${discountInfo.discounted_price.toFixed(2)}`;
                        } else {
                            ticketPrice.innerHTML = `<i class="fas fa-check"></i> ₱${currentTripPrice.toFixed(2)}`;
                        }
                    }
                    this.setCustomValidity('');
                }
            });



            // Form validation
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('submitBtn');
            const seatError = document.getElementById('seatError');

            // Store form reference for later submission
            let bookingForm = null;
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const seatInput = document.getElementById('seat_number');
                const selectedSeat = document.querySelector('.seat.selected');
                const tripSelect = document.getElementById('trip_id');
                const amountInput = document.getElementById('amount');

                if (!tripSelect.value) {
                    tripSelect.focus();
                    return false;
                }

                if (!selectedSeat || !seatInput.value) {
                    seatError.style.display = 'block';
                    seatError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    const seatLayout = document.querySelector('.seat-layout');
                    seatLayout.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                } else {
                    seatError.style.display = 'none';
                }

                if (selectedSeat.classList.contains('booked')) {
                    seatError.textContent = 'This seat is already booked. Please select another seat.';
                    seatError.style.display = 'block';
                    return false;
                }
                
                // Get trip details
                const selectedTrip = tripData.find(trip => trip.id === tripSelect.value);
                if (!selectedTrip) {
                    alert('Please select a trip');
                    return false;
                }
                
                // Calculate discount information
                const basePriceForTrip = parseFloat(selectedTrip.trip_price || selectedTrip.base_price);
                const discountInfo = calculateDiscount(basePriceForTrip, currentUserType);
                const amountPaid = parseFloat(amountInput.value) || 0;
                
                // Populate confirmation modal
                document.getElementById('confirmRoute').textContent = selectedTrip.route_name || 'N/A';
                document.getElementById('confirmDeparture').textContent = selectedTrip.departure_time || 'N/A';
                document.getElementById('confirmSeat').textContent = seatInput.value || 'N/A';
                
                // "Amount to Pay" is the discounted ticket price
                document.getElementById('confirmAmount').textContent = '₱' + discountInfo.discounted_price.toFixed(2);
                
                // "Amount Paid by Customer" is what they actually paid
                document.getElementById('confirmAmountPaid').textContent = '₱' + amountPaid.toFixed(2);
                
                // Display discount information if applicable
                const discountInfoSection = document.getElementById('discountInfoSection');
                if (discountInfo.discount_percent > 0) {
                    discountInfoSection.style.display = 'block';
                    document.getElementById('confirmOriginalPrice').textContent = '₱' + discountInfo.original_price.toFixed(2);
                    document.getElementById('confirmDiscount').textContent = `₱${discountInfo.discount_amount.toFixed(2)} (${discountInfo.discount_percent}% off for ${currentUserType}s)`;
                } else {
                    discountInfoSection.style.display = 'none';
                }
                
                // Calculate and display change
                const changeInfoSection = document.getElementById('changeInfoSection');
                const change = amountPaid - discountInfo.discounted_price;
                if (change > 0) {
                    changeInfoSection.style.display = 'block';
                    document.getElementById('confirmChange').textContent = '₱' + change.toFixed(2);
                } else {
                    changeInfoSection.style.display = 'none';
                }
                
                // Store form reference
                bookingForm = form;
                
                // Show confirmation modal using global function
                showBookingConfirmModal();
            });
            
            // Handle booking confirmation using global hideBookingConfirmModal function
            const confirmBookingBtn = document.getElementById('confirmBookingBtn');
            if (confirmBookingBtn) {
                confirmBookingBtn.addEventListener('click', function() {
                    if (bookingForm) {
                        hideBookingConfirmModal();
                        // Submit the form directly without cloning
                        bookingForm.submit();
                    }
                });
            }

            // Hide seat error when a seat is selected
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('seat') && !e.target.classList.contains('booked')) {
                    seatError.style.display = 'none';
                }
            });
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
    
    <script>
        // Logout confirmation modal
        const logoutLink = document.querySelector('.logout');
        const logoutConfirmModal = document.getElementById('logoutConfirmModal');
        
        if (logoutLink) {
            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                animateModalOpen(logoutConfirmModal);
            });
        }
        
        function hideLogoutConfirmModal() {
            animateModalClose(logoutConfirmModal);
        }
        
        // Close modal when clicking outside
        logoutConfirmModal.addEventListener('click', function(event) {
            if (event.target === logoutConfirmModal) {
                hideLogoutConfirmModal();
            }
        });
    </script>
    @if (!empty($receiptData))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            generateReceipt(@json($receiptData));
        });
    </script>
    @endif
</body>
</html>


