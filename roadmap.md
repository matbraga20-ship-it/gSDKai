# SDK Roadmap (OpenAI Full Coverage)

This roadmap lists what is still missing to achieve a commercially complete SDK aligned with the OpenAI public API surface. Each phase includes concrete deliverables and acceptance criteria. All docs and UI content should remain in English.

## Phase 0 — Audit & Alignment

- [ ] Inventory all OpenAI endpoints and feature groups currently exposed in the SDK.
- [ ] Compare with the official API surface and document gaps.
- [ ] Define a consistent naming scheme for services, DTOs, and API routes.

**Deliverable:** Gap report + naming guide.

## Phase 1 — Responses Core (Start Here)

- [x] Add a dedicated `ResponsesService` wrapper for `/responses`.
- [x] Add a REST demo endpoint `/api/responses` for direct Responses usage.
- [x] Add a Playground tab that allows sending a Responses payload and viewing output.
- [x] Document the endpoint and SDK usage in `docs/API.md` and `docs/SDK.md`.

**Acceptance:** You can create a response through Playground and via SDK without custom code.

## Phase 2 — Assistants Full Coverage

- [x] Expand Assistants coverage to include tools configuration, file attachments, and metadata helpers.
- [x] Add thread messages and run steps examples in docs.
- [x] Add Playground guided flows (assistant → thread → run → result).

**Acceptance:** A full Assistants workflow is available in the demo UI.

## Phase 3 — Files, Uploads, and Vector Stores

- [x] Add explicit Uploads support (multipart upload lifecycle if required).
- [x] Expand vector store file operations (batch add, file status checks).
- [x] Add Playground workflow for uploads → vector store → retrieval.

**Acceptance:** Users can complete file and vector store flows without leaving the UI.

## Phase 4 — Images, Audio, and Vision

- [x] Add remaining image/audio options from the official API (if not already covered).
- [x] Add Playground presets for common media tasks.

**Acceptance:** All media endpoints are exposed in SDK + Playground with presets.

## Phase 5 — Fine-tuning and Batches

- [ ] Expand fine-tuning helpers (validation, event streaming samples).
- [ ] Add batch creation with validation and example payloads.
- [ ] Document recommended workflows and pitfalls.

**Acceptance:** Fine-tuning and batches can be executed end-to-end with examples.

## Phase 6 — Realtime

- [ ] Provide a Realtime session helper plus example client integration.
- [ ] Add documentation for WebRTC or WebSocket usage patterns.

**Acceptance:** Realtime setup is documented and demo instructions are clear.

## Phase 7 — Quality & Commercial Readiness

- [ ] Add automated test suite for core endpoints.
- [ ] Add smoke tests for Playground routes.
- [ ] Polish docs, FAQ, and changelog entries for release.

**Acceptance:** Tests pass and docs are release-grade.
