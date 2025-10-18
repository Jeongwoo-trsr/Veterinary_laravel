# Veterinary Clinic Management System - Database Design

## Database Schema Overview

### Core Tables

#### 1. Users Table (Extended)
- id (Primary Key)
- name
- email (unique)
- email_verified_at
- password
- phone
- address
- role (admin, doctor, pet_owner)
- created_at, updated_at

#### 2. Pet Owners Table
- id (Primary Key)
- user_id (Foreign Key to Users)
- emergency_contact
- emergency_phone
- notes
- created_at, updated_at

#### 3. Pets Table
- id (Primary Key)
- owner_id (Foreign Key to Pet Owners)
- name
- species (dog, cat, bird, etc.)
- breed
- age
- weight
- color
- gender
- microchip_id
- medical_notes
- created_at, updated_at

#### 4. Services Table
- id (Primary Key)
- name (grooming, surgery, therapy, emergency_care, vaccination)
- description
- duration_minutes
- price
- is_active
- created_at, updated_at

#### 5. Doctors Table
- id (Primary Key)
- user_id (Foreign Key to Users)
- specialization
- license_number
- experience_years
- bio
- created_at, updated_at

#### 6. Appointments Table
- id (Primary Key)
- pet_id (Foreign Key to Pets)
- doctor_id (Foreign Key to Doctors)
- service_id (Foreign Key to Services)
- appointment_date
- appointment_time
- status (scheduled, confirmed, in_progress, completed, cancelled)
- notes
- created_at, updated_at

#### 7. Medical Records Table
- id (Primary Key)
- pet_id (Foreign Key to Pets)
- doctor_id (Foreign Key to Doctors)
- appointment_id (Foreign Key to Appointments, nullable)
- diagnosis
- treatment
- prescription
- follow_up_date
- created_at, updated_at

#### 8. Documents Table
- id (Primary Key)
- medical_record_id (Foreign Key to Medical Records)
- file_name
- file_path
- file_type
- file_size
- description
- created_at, updated_at

#### 9. Prescriptions Table
- id (Primary Key)
- medical_record_id (Foreign Key to Medical Records)
- medication_name
- dosage
- frequency
- duration_days
- instructions
- created_at, updated_at

#### 10. Follow-up Schedules Table
- id (Primary Key)
- medical_record_id (Foreign Key to Medical Records)
- scheduled_date
- status (pending, completed, cancelled)
- notes
- created_at, updated_at

## Relationships

1. **Users** → **Pet Owners** (1:1)
2. **Users** → **Doctors** (1:1)
3. **Pet Owners** → **Pets** (1:Many)
4. **Pets** → **Appointments** (1:Many)
5. **Doctors** → **Appointments** (1:Many)
6. **Services** → **Appointments** (1:Many)
7. **Pets** → **Medical Records** (1:Many)
8. **Doctors** → **Medical Records** (1:Many)
9. **Appointments** → **Medical Records** (1:1, nullable)
10. **Medical Records** → **Documents** (1:Many)
11. **Medical Records** → **Prescriptions** (1:Many)
12. **Medical Records** → **Follow-up Schedules** (1:Many)

## Indexes

- Users: email (unique)
- Pets: owner_id, species
- Appointments: pet_id, doctor_id, appointment_date
- Medical Records: pet_id, doctor_id
- Documents: medical_record_id

## Constraints

- Appointment time slots should not overlap for the same doctor
- Pet age should be positive
- Service price should be non-negative
- File uploads should have size limits
- Email addresses should be unique and valid
